<?php

namespace RtclStore\Models;

use DateTime;
use Exception;
use Rtcl\Helpers\Cache;
use Rtcl\Helpers\Functions;
use Rtcl\Models\Payment;
use Rtcl\Resources\Options as RtclOptions;
use RtclStore\Helpers\Functions as StoreFunctions;
use WP_Error;
use WP_User;

class Membership {

    protected int $id = 0;
    protected int $user_id = 0;
    protected $ads_as_free;
    protected $posted_ads_as_free = null;
    protected $remaining_ads_as_free;
    protected $Subscription;

    protected $status;
    protected $active_membership;
    protected string $table;
    protected string $table_meta;
    protected string $table_posting_log;
    protected $metas;
    protected $settings;

    /**
     * @param int|WP_User $user
     * @param int|Membership $membershipId
     * @throws Exception
     */
    function __construct( $user, $membershipId = null ) {

        if ( is_int( $user ) ) {
            $this->user_id = $user;
        } else if ( is_a( $user, WP_User::class ) ) {
            $this->user_id = $user->ID;
        } else if ( $membershipId ) {
            $this->id = $membershipId;
        } else {
            throw new Exception( esc_html__( 'User or id can not be empty! Either WP_User::class or user id' ) );
        }
        global $wpdb;
        $this->table = $wpdb->prefix . "rtcl_membership";
        $this->table_meta = $wpdb->prefix . "rtcl_membership_meta";
        $this->table_posting_log = $wpdb->prefix . "rtcl_posting_log";
        $this->set_membership_data();
    }

    private function set_membership_data() {
        global $wpdb;
        if ( $this->user_id ) {
            $membership = $wpdb->get_row(
                $wpdb->prepare( "SELECT * FROM {$this->table} WHERE user_id = %d", $this->user_id )
            );
        } elseif ( $this->id ) {
            $membership = $wpdb->get_row(
                $wpdb->prepare( "SELECT * FROM {$this->table} WHERE id = %d", $this->id )
            );
        }

        if ( null !== $membership ) {
            $this->id = $membership->id;
            $membership->categories = $this->get_meta( 'membership_categories' );
            $promotions = $this->get_meta( '_rtcl_promotions', true );
            $membership->promotions = !empty( $promotions ) ? array_filter( $promotions, function ( $promotion ) {
                return !empty( $promotion['ads'] ) && absint( $promotion['ads'] ) && !empty( $promotion['validate'] ) && absint( $promotion['validate'] );
            } ) : [];
            $this->Subscription = $membership;
        }

    }

    public function cacheClear() {
        $cache_key = Cache::get_cache_prefix( 'membership' ) . 'member_' . $this->user_id;
        $cache_id_key = Cache::get_cache_prefix( 'membership' ) . 'id_' . $this->id;
        wp_cache_delete( $cache_key, 'membership' );
        wp_cache_delete( $cache_id_key, 'membership' );
    }

    public function has_membership(): bool {
        return !empty( $this->id );
    }

    public function get_id(): int {
        return $this->id;
    }

    public function get_user_id(): int {
        return $this->has_membership() ? $this->Subscription->user_id : 0;
    }

    /**
     * Will return subscriptions
     *
     * @return object | bool
     */
    public function get_subscription() {
        return $this->has_membership() ? $this->Subscription : false;
    }

    /**
     * Promotions
     *
     * @return array
     */
    public function get_promotions() {
        return $this->has_membership() ? $this->get_subscription()->promotions : [];
    }

    public function is_active() {
        return $this->Subscription->active;
    }

    public function get_expiry_date() {
        return $this->Subscription->expiry_date;
    }

    public function get_remaining_ads() {
        return $this->Subscription->ads;
    }

    public function get_remaining_free_ads() {
        return $this->hasUnlimitedFreeAds() ? -1 : StoreFunctions::user_is_valid_to_post_as_free( $this->user_id );
    }

    public function get_posted_ads() {
        return $this->Subscription->posted_ads;
    }

    /**
     * @return integer Number of remaining post
     */
    public function is_valid_to_post_as_free(): int {
        $this->set_settings();
        if ( isset( $this->settings['enable_free_ads'] ) && $this->settings['enable_free_ads'] == "yes" ) {
            if ( isset( $this->settings['unlimited_free_ads_membership'] ) && 'yes' === $this->settings['unlimited_free_ads_membership'] ) {
                return 99999; // unlimited post count :p
            }
            $ads = StoreFunctions::get_posted_ads_as_free( $this->user_id );
            $limit_ads = isset( $this->settings['number_of_free_ads'] ) ? absint( $this->settings['number_of_free_ads'] ) : 3;
            $remaining = $limit_ads - $ads;
            $this->remaining_ads_as_free = $remaining && $remaining > 0 ? $remaining : 0;

            return $this->remaining_ads_as_free ?: 0;
        }

        return 0;
    }


    /**
     * @param int $cat_id
     *
     * @return bool
     */
    public function is_valid_to_post_at_category_as_free( int $cat_id ): bool {
        if ( StoreFunctions::user_is_valid_to_post_as_free() ) {
            $this->cacheClear();

            return $this->is_valid_for_free( $cat_id );
        }

        return false;
    }

    /**
     * @param int $cat_id
     *
     * @return bool
     */
    public function is_valid_for_free( int $cat_id ): bool {

        $this->set_settings();
        $cats = isset( $this->settings['categories_of_free_ads'] ) && is_array( $this->settings['categories_of_free_ads'] ) ? $this->settings['categories_of_free_ads'] : [];
        if ( empty( $cats ) ) {
            return true;
        }
        $parents = get_ancestors( $cat_id, rtcl()->category, 'taxonomy' );
        if ( !empty( $parents ) ) {
            $parents = array_reverse( $parents );
            $cat_id = $parents[0];
        }

        return in_array( $cat_id, $cats );
    }

    /**
     * @return integer Number of remaining post
     */
    public function is_valid_to_post(): int {
        if ( !$this->is_expired() ) {
            $remaining = absint( $this->Subscription->ads );

            return $remaining && $remaining > 0 ? $remaining : 0;
        }

        return 0;
    }

    /**
     * @param integer $cat_id
     *
     * @return bool
     */
    public function is_valid_to_post_at_category( int $cat_id ): bool {
        if ( $this->is_valid_to_post() ) {
            $cats = $this->get_meta( 'membership_categories' );
            if ( empty( $cats ) ) {
                return true;
            }
            $parents = get_ancestors( $cat_id, rtcl()->category, 'taxonomy' );
            if ( !empty( $parents ) ) {
                $parents = array_reverse( $parents );
                $cat_id = $parents[0];
            }

            return in_array( $cat_id, $cats );
        }

        return false;
    }

    public function hasUnlimitedFreeAds(): bool {
        $this->set_settings();

        return isset( $this->settings['enable_free_ads'] ) && $this->settings['enable_free_ads'] == "yes" && isset( $this->settings['unlimited_free_ads_membership'] ) && 'yes' === $this->settings['unlimited_free_ads_membership'];
    }

    /**
     * @return int|mixed|string|null
     * @throws Exception
     * @deprecated
     */
    public function get_posted_ads_as_free() {
        if ( null === $this->posted_ads_as_free ) {
            if ( $this->user_id ) {
                global $wpdb;
                $settings = Functions::get_option( 'rtcl_membership_settings' );
                $days = 30; // default days
                if ( isset( $settings['renewal_days_for_free_ads'] ) && ( $settings['renewal_days_for_free_ads'] !== '' ) ) {
                    $days = absint( $settings['renewal_days_for_free_ads'] );
                }
                $current_date = new \DateTime( current_time( 'mysql' ) );
                $end_date = $current_date->format( 'Y-m-d H:i:s' );
                $current_date->sub( new \DateInterval( "P{$days}D" ) );
                $start_date = $current_date->format( 'Y-m-d H:i:s' );

                $this->posted_ads_as_free = $wpdb->get_var(
                    $wpdb->prepare( "SELECT COUNT(*) FROM 
											{$this->table_posting_log} 
											WHERE user_id = %d 
											AND (created_at BETWEEN %s AND %s)",
                        $this->user_id,
                        $start_date,
                        $end_date
                    )
                );
            } else {
                $this->posted_ads_as_free = 0;
            }
            $this->cacheClear();
        }

        return $this->posted_ads_as_free;
    } // Not used

    /**
     * @return int|string
     * @deprecated
     */
    public function get_remaining_ads_as_free() {
        if ( !$this->remaining_ads_as_free ) {
            $this->remaining_ads_as_free = StoreFunctions::user_is_valid_to_post_as_free();
            $this->cacheClear();
        }

        return $this->remaining_ads_as_free;
    }

    // Not used
    public function get_since() {
        if ( !$this->id ) {
            return $this->Subscription->member_since;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function is_expired(): bool {
        if ( !$this->has_membership() ) {
            return true;
        }
        try {
            $current_date = new DateTime( current_time( 'mysql' ) );
            $expiry_date = new DateTime( Functions::datetime( 'mysql', trim( $this->Subscription->expiry_date ) ) );
            if ( $current_date > $expiry_date ) {
                return true;
            }
        } catch ( Exception $e ) {
            return true;
        }

        return false;
    }

    /**
     * Membership remaining days
     *
     * @return int
     */
    public function remaining_days() {
        if ( $this->has_membership() && $this->get_expiry_date() ) {
            $from = current_time( 'timestamp' );
            $to = strtotime( $this->get_expiry_date() );
            $diff = (int)abs( $to - $from );
            $days = round( $diff / DAY_IN_SECONDS );

            return $days < 1 ? 0 : $days;
        }

        return 0;
    }

    /**
     * @param Payment $payment
     *
     * @throws \Exception
     */
    public function apply_membership( Payment $payment ) {
        if ( $this->id ) {
            $this->update_membership( $payment );
        } else {
            $this->add_membership( $payment );
        }
    }

    /**
     * @param array $data
     * @param WP_Error $errors
     *
     * @return array
     */
    public function apply_promotion( array $data, WP_Error $errors ): array {
        $listing_id = isset( $data['listing_id'] ) ? absint( $data['listing_id'] ) : '';
        $raw_promotions = [];
        if ( isset( $data['promotions'] ) && is_array( $data['promotions'] ) && !empty( $data['promotions'] ) ) {
            $existing_array_keys = array_keys( RtclOptions::get_listing_promotions() );
            $raw_promotions = array_filter( $data['promotions'], function ( $promotion_item ) use ( $existing_array_keys ) {
                return in_array( $promotion_item, $existing_array_keys, true );
            } );
        }
        $response = [ 'success' => false ];
        if ( empty( $raw_promotions ) ) {
            $errors->add( 'rtcl_membership_promotion_invalid_promotion', esc_html__( "Please select a membership promotion. membership_promotions is missing", "classified-listing-store" ) );
        }
        $listing = rtcl()->factory->get_listing( $listing_id );
        if ( !$listing ) {
            $errors->add( 'rtcl_membership_promotion_invalid_listing', esc_html__( "Please select an ad. listing_id missing", "classified-listing-store" ) );
        }

        if ( $this->is_expired() || empty( $promotions = $this->get_promotions() ) ) {
            $errors->add( 'rtcl_membership_promotion_invalid_listing', esc_html__( "You have not membership or any promotions to promote this ad.", "classified-listing-store" ) );
        }
        $promotions_data = [];
        if ( !$errors->has_errors() ) {
            foreach ( $raw_promotions as $raw_promotion ) {
                if ( empty( $promotions[$raw_promotion]['ads'] ) || empty( absint( $promotions[$raw_promotion]['validate'] ) ) || empty( $ads = absint( $promotions[$raw_promotion]['ads'] ) ) || empty( $validate = absint( $promotions[$raw_promotion]['validate'] ) ) ) {
                    $errors->add( 'rtcl_membership_promotion_no_' . $raw_promotion . '_promotion', sprintf( __( "You have no %s promotion left.", "classified-listing-store" ), RtclOptions::get_listing_promotions()[$raw_promotion] ) );
                } else {
                    $promotions_data[$raw_promotion] = absint( $promotions[$raw_promotion]['validate'] );
                }
            }
        }

        do_action( 'rtcl_membership_promotion_before_apply', $data, $this, $errors, $response );

        if ( !$errors->has_errors() && !empty( $promotions_data ) ) {
            $applied_status = [ 'publish', 'rtcl-expired' ];
            if ( Functions::get_option_item( 'rtcl_moderation_settings', 'pending_listing_status_after_promotion', false, 'checkbox' ) ) {
                $applied_status[] = 'pending';
            }
            if ( in_array( $listing->get_status(), $applied_status, true ) ) {
                $promotion_status = Functions::update_listing_promotions( $listing_id, $promotions_data );
                $promotion_status = apply_filters( 'rtcl_store_update_listing_membership_promotion', $promotion_status, $listing_id, $promotions_data );
                if ( !empty( $promotion_status ) ) {
                    // Check if post expired or pending, then turn it to published
                    if ( in_array( $listing->get_status(), $applied_status, true ) && "publish" !== $listing->get_status() ) {
                        wp_update_post( [
                            'ID'          => $listing_id,
                            'post_status' => 'publish'
                        ] );
                    }
                }
            } else {
                $pending_promotions = get_post_meta( $listing_id, '_rtcl_pending_promotions', true );
                if ( is_array( $pending_promotions ) && !empty( $pending_promotions ) ) {
                    foreach ( $promotions_data as $promotion_key => $promotions_validate ) {
                        if ( isset( $pending_promotions[$promotion_key] ) ) {
                            $pending_promotions[$promotion_key] = absint( $pending_promotions[$promotion_key] ) + absint( $promotions_validate );
                        } else {
                            $pending_promotions[$promotion_key] = absint( $promotions_validate );
                        }
                    }
                } else {
                    $pending_promotions = $promotions_data;
                }

                update_post_meta( $listing_id, '_rtcl_pending_promotions', $pending_promotions );
            }
            foreach ( $promotions_data as $promotion_key => $promotion_validate ) {
                $updated_ads = absint( $promotions[$promotion_key]['ads'] ) - 1;
                if ( $updated_ads > 0 ) {
                    $promotions[$promotion_key]['ads'] = $updated_ads;
                } else {
                    unset( $promotions[$promotion_key] );
                }
            }
            $this->update_meta( '_rtcl_promotions', $promotions );
            $response['success'] = true;
            do_action( 'rtcl_membership_promotion_apply', $data, $this, $errors, $response );
            $this->cacheClear();
        }
        do_action( 'rtcl_membership_promotion_after_apply', $data, $this, $errors, $response );

        return $response;
    }

    /**
     * @param Payment $payment
     *
     * @throws \Exception
     */
    public function update_membership( Payment $payment ) {
        if ( $this->id ) {
            $pricing = $payment->pricing;
            $new_ads = absint( get_post_meta( $pricing->getId(), 'regular_ads', true ) );
            $data = [];
            $expired = true;
            $days = absint( $pricing->getVisible() );
            $current_date = new \DateTime( current_time( 'mysql' ) );
            $expiry_date = new \DateTime( Functions::datetime( 'mysql', trim( $this->Subscription->expiry_date ) ) );
            if ( apply_filters( 'rtcl_store_membership_carry_forward', true ) && ( $current_date < $expiry_date || apply_filters( 'rtcl_store_expired_membership_carry_forward', false ) ) ) {
                if ( $current_date > $expiry_date ) {
                    $current_date->add( new \DateInterval( "P{$days}D" ) ); // added for when expired
                    $data['expiry_date'] = $current_date->format( 'Y-m-d H:i:s' );
                } else {
                    $expiry_date->add( new \DateInterval( "P{$days}D" ) ); // work for not expire
                    $data['expiry_date'] = $expiry_date->format( 'Y-m-d H:i:s' );
                }
                $data['ads'] = absint( $this->Subscription->ads ) + $new_ads;
                $expired = false;
            } else {
                $current_date->add( new \DateInterval( "P{$days}D" ) );
                $data['expiry_date'] = $current_date->format( 'Y-m-d H:i:s' );
                $data['ads'] = $new_ads;
            }
            global $wpdb;

            $where = [
                'id'      => $this->id,
                'user_id' => $this->user_id
            ];
            $update = $wpdb->update(
                $this->table,
                $data,
                $where
            );
            if ( $update ) {
                $payment->set_applied();
                $this->set_membership_data();
                $promotions = get_post_meta( $payment->get_id(), "_rtcl_membership_promotions", true );
                $promotions = is_array( $promotions ) && !empty( $promotions ) ? $promotions : [];
                $cats = get_post_meta( $pricing->getId(), 'membership_categories', true );
                // No carry forward for category
                $this->delete_meta( 'membership_categories' );
                if ( is_array( $cats ) && !empty( $cats ) ) {
                    foreach ( $cats as $cat ) {
                        if ( absint( $cat ) ) {
                            $this->add_meta( 'membership_categories', $cat );
                        }
                    }
                }

                if ( $expired ) {
                    if ( !empty( $promotions ) ) {
                        $this->update_meta( '_rtcl_promotions', $promotions );
                    } else {
                        $this->delete_meta( '_rtcl_promotions' );
                    }
                } else {
                    $mPromotions = $this->get_meta( '_rtcl_promotions', true );
                    $mPromotions = is_array( $mPromotions ) && !empty( $mPromotions ) ? $mPromotions : [];
                    if ( !empty( $mPromotions ) ) {
                        $big_promotions = count( $mPromotions ) > count( $promotions ) ? $mPromotions : $promotions;
                        $new_promotions = [];
                        foreach ( $big_promotions as $promotion_key => $promotion ) {
                            if ( !empty( $mPromotions[$promotion_key]['ads'] ) && $old_ads = absint( $mPromotions[$promotion_key]['ads'] ) ) {
                                $new_ads = ( !empty( $promotions[$promotion_key]['ads'] ) ? absint( $promotions[$promotion_key]['ads'] ) : 0 ) + $old_ads;
                                $new_validate = !empty( $promotions[$promotion_key]['validate'] ) ? absint( $promotions[$promotion_key]['validate'] ) : 0;
                                $new_validate = !$new_validate && !empty( $mPromotions[$promotion_key]['validate'] ) ? absint( $mPromotions[$promotion_key]['validate'] ) : $new_validate;
                                if ( $new_ads && $new_validate ) {
                                    $new_promotions[$promotion_key]['ads'] = $new_ads;
                                    $new_promotions[$promotion_key]['validate'] = $new_validate;
                                }
                            } else {
                                if ( !empty( $promotions[$promotion_key]['ads'] ) && !empty( $promotions[$promotion_key]['validate'] ) ) {
                                    $new_promotions[$promotion_key]['ads'] = absint( $promotions[$promotion_key]['ads'] );
                                    $new_promotions[$promotion_key]['validate'] = absint( $promotions[$promotion_key]['validate'] );
                                }
                            }
                        }
                        if ( !empty( $new_promotions ) ) {
                            $this->update_meta( '_rtcl_promotions', $new_promotions );
                        } else {
                            $this->delete_meta( '_rtcl_promotions' );
                        }
                    } elseif ( !empty( $promotions ) ) {
                        $this->update_meta( '_rtcl_promotions', $promotions );
                    } else {
                        $this->delete_meta( '_rtcl_promotions' );
                    }
                }

                $order_ids = $this->get_meta( '_rtcl_order_ids', true );
                $order_ids = !empty( $order_ids ) && is_array( $order_ids ) ? $order_ids : [];
                $order_ids[] = $payment->get_id();

                $this->update_meta( '_rtcl_order_ids', $order_ids );

                $this->cacheClear();
                do_action( 'rtcl_membership_updated', $this, $payment );
            }
        }
    }

    /**
     * @param array $rawData
     * @return array| WP_Error
     */
    public function update_membership_data( $rawData ) {

        $placeholder = [ 'ads', 'posted_ads', 'expiry_date' ];
        $data = [];
        foreach ( $placeholder as $itemKey ) {
            if ( isset( $rawData[$itemKey] ) ) {
                $value = null;
                if ( in_array( $itemKey, [ 'ads', 'posted_ads' ] ) ) {
                    $value = absint( $rawData[$itemKey] );
                } elseif ( in_array( $itemKey, [ 'expiry_date', 'member_since' ] ) ) {
                    $format = 'Y-m-d H:i:s';
                    $d = DateTime::createFromFormat( $format, $rawData[$itemKey] );
                    if ( $d && $d->format( $format ) === $rawData[$itemKey] ) {
                        $value = $d->format( $format );
                    }
                }
                if ( $value !== null && $value !== $this->get_subscription()->{$itemKey} ) {
                    $data[$itemKey] = $value;
                }
            }
        }

        if ( empty( $data ) ) {
            return new WP_Error( 'rtcl_membership_missing_required_data', __( 'Missing data to update.', 'classified-listing-store' ), [ 'status' => 400 ] );
        }

        global $wpdb;
        $where = [
            'id'      => $this->id,
            'user_id' => $this->get_user_id()
        ];
        $update = $wpdb->update(
            $this->table,
            $data,
            $where
        );

        if ( !$update ) {
            return new WP_Error( 'rtcl_membership_updating_error', __( 'Error while updating.', 'classified-listing-store' ), [ 'status' => 400 ] );
        }

        $this->cacheClear();

        return $data;
    }

    /**
     * @param Payment $payment
     *
     * @throws \Exception
     */
    public function add_membership( Payment $payment ) {
        if ( !$this->id ) {
            $pricing = $payment->pricing;
            $ads = absint( get_post_meta( $pricing->getId(), 'regular_ads', true ) );
            $days = absint( $pricing->getVisible() );
            $current_date = new \DateTime( current_time( 'mysql' ) );
            $current_date->add( new \DateInterval( "P{$days}D" ) );
            $data = [
                'user_id'      => $this->user_id,
                'ads'          => $ads,
                'expiry_date'  => $current_date->format( 'Y-m-d H:i:s' ),
                'member_since' => current_time( 'mysql' )
            ];
            global $wpdb;
            $wpdb->insert(
                $this->table,
                $data,
                [
                    '%d',
                    '%d',
                    '%s',
                    '%s'
                ]
            );
            if ( $wpdb->insert_id ) {
                $payment->set_applied();
                $this->set_membership_data();
                $cats = get_post_meta( $pricing->getId(), 'membership_categories', true );
                if ( is_array( $cats ) && !empty( $cats ) ) {
                    foreach ( $cats as $cat ) {
                        $this->add_meta( 'membership_categories', $cat );
                    }
                }
                $promotions = get_post_meta( $payment->get_id(), "_rtcl_membership_promotions", true );
                if ( is_array( $promotions ) && !empty( $promotions ) ) {
                    $this->update_meta( '_rtcl_promotions', $promotions );
                }

                $this->update_meta( '_rtcl_order_ids', [ $payment->get_id() ] );

                $this->cacheClear();
                do_action( 'rtcl_membership_added', $this, $payment );
            }
        }
    }

    public function update_meta( $meta_key, $meta_value, $prev_value = '' ) {

        if ( !$this->id || !$meta_key ) {
            return false;
        }

        // expected_slashed ($meta_key)
        $raw_meta_key = $meta_key;
        $meta_key = wp_unslash( $meta_key );
        $passed_value = $meta_value;
        $meta_value = wp_unslash( $meta_value );
        $meta_value = sanitize_meta( $meta_key, $meta_value, 'post' );

        // Compare existing value to new value if no prev value given and the key exists only once.
        if ( empty( $prev_value ) ) {
            $old_value = $this->get_meta( $meta_key );
            if ( count( $old_value ) == 1 ) {
                if ( $old_value[0] === $meta_value ) {
                    return false;
                }
            }
        }
        global $wpdb;
        $meta_ids = $wpdb->get_col( $wpdb->prepare( "SELECT meta_id FROM {$this->table_meta} WHERE meta_key = %s AND membership_id = %d", $meta_key, $this->id ) );
        if ( empty( $meta_ids ) ) {
            return $this->add_meta( $raw_meta_key, $passed_value );
        }

        $meta_value = maybe_serialize( $meta_value );

        $result = $wpdb->update(
            $this->table_meta,
            compact( 'meta_value' ),
            [ 'membership_id' => $this->id, 'meta_key' => $meta_key ]
        );
        if ( !$result ) {
            return false;
        }

        $this->cacheClear();

        return true;
    }

    public function add_meta( $meta_key, $meta_value, $unique = false ) {

        if ( !$this->id || !$meta_key ) {
            return false;
        }

        // expected_slashed ($meta_key)
        $meta_key = wp_unslash( $meta_key );
        $meta_value = wp_unslash( $meta_value );
        $meta_value = sanitize_meta( $meta_key, $meta_value, 'post' );
        global $wpdb;
        // Only unique
        if ( $unique && $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->table_meta} WHERE meta_key = %s AND membership_id = %d",
                $meta_key, $this->id ) ) ) {
            return false;
        }

        $meta_value = maybe_serialize( $meta_value );

        $result = $wpdb->insert( $this->table_meta, [
            'membership_id' => $this->id,
            'meta_key'      => $meta_key,
            'meta_value'    => $meta_value
        ] );

        if ( !$result ) {
            return false;
        }

        $this->cacheClear();

        return $wpdb->insert_id;
    }

    public function get_meta( $meta_key, $single = false ) {
        if ( !$this->id || !$meta_key ) {
            return false;
        }
        global $wpdb;
        $metas = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT meta_value FROM {$this->table_meta} 
                                    WHERE  membership_id = %d
                                    AND meta_key = %s",
                $this->id,
                $meta_key
            )
        );
        if ( $metas ) {
            if ( $single ) {
                return maybe_unserialize( $metas[0] );
            } else {
                return array_map( 'maybe_unserialize', $metas );
            }
        }


        if ( $single ) {
            return '';
        } else {
            return [];
        }

    }

    public function delete_meta( $meta_key, $meta_value = '' ) {

        if ( !$this->id || !$meta_key ) {
            return false;
        }

        $meta_key = wp_unslash( $meta_key );
        $meta_value = wp_unslash( $meta_value );
        $meta_value = maybe_serialize( $meta_value );
        global $wpdb;
        $query = $wpdb->prepare( "SELECT meta_id FROM {$this->table_meta} 
                                              WHERE membership_id = %d
                                              AND meta_key = %s",
            $this->id,
            $meta_key
        );
        if ( '' !== $meta_value && null !== $meta_value && false !== $meta_value ) {
            $query .= $wpdb->prepare( " AND meta_value = %s", $meta_value );
        }
        $meta_ids = $wpdb->get_col( $query );
        if ( !count( $meta_ids ) ) {
            return false;
        }
        $query = "DELETE FROM {$this->table_meta} WHERE meta_id IN( " . implode( ',', $meta_ids ) . " )";

        $count = $wpdb->query( $query );
        if ( !$count ) {
            return false;
        }

        $this->cacheClear();

        return true;

    }

    private function set_settings() {
        if ( !$this->settings ) {
            $this->settings = Functions::get_option( 'rtcl_membership_settings' );
        }
    }

    public function update_post_count() {

        $posted_ads = absint( $this->Subscription->posted_ads ) + 1;
        $data = [
            'posted_ads' => $posted_ads
        ];
        if ( $this->is_valid_to_post() ) {
            $ads = absint( $this->Subscription->ads ) - 1;
            $ads = $ads < 0 ? 0 : $ads;
            $data['ads'] = $ads;
        }
        global $wpdb;
        $wpdb->update(
            $this->table,
            $data,
            [
                'user_id' => $this->user_id,
                'id'      => $this->id,
            ]
        );

        $this->cacheClear();
    }

    /**
     * @param int $count
     * @return void
     */
    public function add_post_count($count = 1) {

        $posted_ads = absint( $this->Subscription->posted_ads ) - $count;
        $ads = absint( $this->Subscription->ads ) + $count;
        $data = [
            'posted_ads' => max( $posted_ads, 0 ),
            'ads'        => max( $ads, 0 )
        ];
        error_log(print_r($data, true). "\n", 3, ABSPATH ."wp-content/logs.log");
        
        global $wpdb;
        $wpdb->update(
            $this->table,
            $data,
            [
                'user_id' => $this->user_id,
                'id'      => $this->id,
            ]
        );

        $this->cacheClear();
    }

}
