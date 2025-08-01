<?php

namespace Wpcb2\FunctionalityPlugin;


use Wpcb2\FunctionalityPlugin\Service\FileSystem;
use Wpcb2\FunctionalityPlugin\Service\Slugifier;
use Wpcb2\Repository\SnippetRepository;
use Wpcb2\Snippet\GlobalCSS;
use Wpcb2\Snippet\GlobalJS;
use Wpcb2\Snippet\Snippet;
use Wpcb2\Snippet\SnippetFactory;

class Manager
{

	private $path;
	private $mainFile;
	private $wpcodeboxPath = WPCODEBOX2_PATH;

	private $pluginName;

	private $fileSystemService;

	private $slugifier;

	private $snippetRepository;

	private $isTemp = false;

	private $pluginDisplayName = '';

	private $pluginDisplayDescription = '';

	public static $isTempPlugin = false;

	public static $tempPluginName = 'My Custom Functionality Plugin';

	public function __construct($pluginName, $isTemp = false)
	{
		$this->isTemp = $isTemp;
		$this->slugifier = new Slugifier();
		$this->fileSystemService = new FileSystem();
		$this->snippetRepository = new SnippetRepository();


		if (!$pluginName) {
			if (!$this->isTemp) {
				if(defined('WPCB_FP_PLUGIN_NAME')) {
					$pluginName = WPCB_FP_PLUGIN_NAME;
				} else {
					$pluginName = 'WPCodeBox Functionality Plugin';
				}
			} else {
				$pluginName = 'My Custom Functionality Plugin';
			}
			if(defined('WPCB_FP_PLUGIN_DESCRIPTION')) {
				$this->pluginDisplayDescription = WPCB_FP_PLUGIN_DESCRIPTION;
			} else {
				$this->pluginDisplayDescription = 'This plugin is generated by WPCodeBox. It is used to execute your Code Snippets.';
			}

			$this->pluginDisplayName = $pluginName;
		} else {
			if($isTemp) {
				self::$tempPluginName = $pluginName;
			}
		}


		$this->pluginName = $this->slugifier->slugify($pluginName);
		if (!$isTemp) {
			$this->path = WP_PLUGIN_DIR . '/' . $this->pluginName . '/';
		} else {
			self::$isTempPlugin = true;
			$this->path = get_temp_dir() . $this->pluginName . '/';

			if(!is_dir($this->path)) {
				mkdir($this->path);
			}

			if (!is_dir($this->path . $this->pluginName)) {
				mkdir($this->path . $this->pluginName);
			}

			$this->path = $this->path . $this->pluginName . '/';
		}

		$this->mainFile = $this->path . 'plugin.php';

	}

	public function getCodeUrl()
	{
		return '<?php echo plugin_dir_url("' . $this->pluginName . '/plugin.php");?>';
	}

	public function isEnabled()
	{
		return is_dir($this->path);
	}

	public function assetsPath($path = '')
	{
		return $this->path . 'assets/' . $path;
	}

	public function snippetsPath($path = '')
	{
		return $this->path . 'snippets/' . $path;
	}

	public function enable()
	{
		if (!is_writable(WP_PLUGIN_DIR)) {
			throw new PluginsFolderNotWritableException();
		}

		try {

			$this->fileSystemService->copyFolder(
				$this->wpcodeboxPath . 'templates/fp/',
				$this->path
			);


		} catch (\Throwable $e) {
			echo $e->getMessage();
			die;
		}

		$mainFileContents = file_get_contents($this->mainFile);
		$mainFileContents = str_replace('{PLUGIN_NAME}', $this->pluginDisplayName, $mainFileContents);
		$mainFileContents = str_replace('{PLUGIN_DESCRIPTION}', $this->pluginDisplayDescription, $mainFileContents);
		$mainFileContents = str_replace('{PLUGIN_VERSION}', '1.0.0', $mainFileContents);

		file_put_contents($this->mainFile, $mainFileContents);

		$this->copySnippets();

		$activePlugins = get_option('active_plugins');
		$activePlugins = maybe_unserialize($activePlugins);
		$activePlugins[] = $this->slugify($this->pluginDisplayName) . '/plugin.php';
		update_option('active_plugins', $activePlugins);
	}

	public function generatePlugin($snippetIds, $pluginName, $pluginDescription, $pluginAuthor, $version)
	{
		$sysDir = get_temp_dir();
		$sysDir = str_replace("\\", "/", $sysDir);

		if(empty($pluginName)) {
			$pluginName = 'My Custom Functionality Plugin';
		}
		try {

			$this->fileSystemService->copyFolder(
				$this->wpcodeboxPath . 'templates/gp/',
				$this->path
			);

		} catch (\Throwable $e) {
			echo $e->getMessage();
			die;
		}

		$mainFileContents = file_get_contents($this->mainFile);
		$mainFileContents = str_replace('{PLUGIN_NAME}', $pluginName, $mainFileContents);
		$mainFileContents = str_replace('{PLUGIN_DESCRIPTION}', $pluginDescription, $mainFileContents);
		$mainFileContents = str_replace('{PLUGIN_VERSION}', $version, $mainFileContents);
		$mainFileContents = str_replace('{PLUGIN_AUTHOR}', $pluginAuthor, $mainFileContents);
		file_put_contents($this->mainFile, $mainFileContents);
		foreach ($snippetIds as $snippetId) {
			$snippetId = intval($snippetId);
			$this->saveSnippet($snippetId);
		}

		$pluginName = $this->slugify($pluginName);

		$this->fileSystemService->zipData($sysDir . $pluginName, $sysDir . $pluginName . '/plugin.zip');
		$plugin = file_get_contents($sysDir . $pluginName . '/plugin.zip');
		$this->fileSystemService->recursiveRemoveDirectory($sysDir . $pluginName);

		return $plugin;

	}

	public function disable()
	{
		deactivate_plugins($this->slugify($this->pluginDisplayName) . '/plugin.php');
		delete_plugins([$this->slugify($this->pluginDisplayName) . '/plugin.php']);
	}

	function updateStatus($snippetId, $status)
	{
		if (!$this->isEnabled()) {
			return false;
		}

		$snippet = $this->createInternalSnippet($snippetId);
		$fileName = $snippet->getMainFileName();
		$mainFileContent = file_get_contents($this->mainFile);

		$snippetIncludeDisabled = $this->getSnippetFileNameDisabled($fileName) . "\n";
		$snippetIncludeEnabled = $this->getSnippetFileName($fileName) . "\n";

		if ($snippet->getCodeType() === 'php' && $snippet->getRunType() === 'never') {
			$status = false;
		}

		if ($status === 1) {
			$mainFileContent = str_replace($snippetIncludeDisabled, $snippetIncludeEnabled, $mainFileContent);
		} else {
			$mainFileContent = str_replace($snippetIncludeEnabled, $snippetIncludeDisabled, $mainFileContent);

		}
		file_put_contents($this->mainFile, $mainFileContent);

		return true;
	}

	function disableSnippet($snippetId)
	{
		if (!$this->isEnabled()) {
			return false;
		}

		$snippet = $this->createInternalSnippet($snippetId);

		$fileName = $snippet->getFileName();

		$mainFileContent = file_get_contents($this->mainFile);

		$snippetFileName = $this->getSnippetFileName($fileName, $snippetId);
		$snippetFileNameDisable = $this->getSnippetFileNameDisabled($fileName, $snippetId);

		$mainFileContent = str_replace($snippetFileName . "\n", $snippetFileNameDisable . "\n", $mainFileContent);

		file_put_contents($this->mainFile, $mainFileContent);

		return true;
	}


	public function saveSnippet($snippetId)
	{
		if (!$this->isEnabled()) {
			return false;
		}


		$snippet = $this->createInternalSnippet($snippetId);

		if ($snippet->getRunType() && $snippet->getRunType() === 'once') {
			$this->signSnippet($snippetId);
			return false;
		}

		if ($snippet->getCodeType() === 'txt') {
			return false;
		}


		$enabled = !!$snippet->isEnabled();

		// Always enable snippets when you are generating a plugin
		if ($this->isTemp) {
			$enabled = true;
		}

		if ($snippet->isAsset() && $snippet->getRenderType() !== 'external') {

			$extension = $snippet->getCodeType();
			if ($extension === 'scss' || $extension === 'less') {
				$extension = 'css';
			}

			if (file_exists($this->assetsPath() . $extension . '/' . $snippet->getFileName())) {
				unlink($this->assetsPath() . $extension . '/' . $snippet->getFileName());
				unlink($this->snippetsPath() . $snippet->getMainFileName());

				$mainFileContent = file_get_contents($this->mainFile);

				$mainFileContent = str_replace($this->getSnippetFileName($snippet->getMainFileName()), '', $mainFileContent);
				$mainFileContent = str_replace($this->getSnippetFileNameDisabled($snippet->getMainFileName()), '', $mainFileContent);

				file_put_contents($this->mainFile, $mainFileContent);

			}
		}

		if ($snippet->getRenderType() === 'external' || $snippet->getRenderType() === 'none') {

			$extension = $snippet->getCodeType();
			if ($extension === 'scss' || $extension === 'less') {
				$extension = 'css';
			}

			$folderName = $snippet->getFolderName();

			if (!is_dir($this->assetsPath() . $extension . '/' . $folderName)) {
				mkdir($this->assetsPath() . $extension . '/' . $folderName);
				touch($this->assetsPath() . $extension . '/' . $folderName . '/index.php');
			}

			$destinationFile = $this->assetsPath() . $extension . '/' . $snippet->getFileName();

			file_put_contents($destinationFile, $snippet->getCompiledCode());

		}

		try {
			$code = $snippet->getCode();
		} catch (\Exception $e) {
			echo $e->getMessage();
			die;
		}


		if ($snippet->getCodeType() !== 'json') {
			$code = "<?php if(!defined('ABSPATH')) { die(); }  \n\n" . $code;
		}
		$conditionBuilderCode = $snippet->getFPConditionCode();

		$code = str_replace("{{WPCB_CONDITION_CODE}}", $conditionBuilderCode, $code);

		if ($snippet->getFolderName() && !is_dir($this->path . 'snippets/' . $snippet->getFolderName())) {
			mkdir($this->snippetsPath() . $snippet->getFolderName());
			touch($this->snippetsPath() . $snippet->getFolderName() . '/index.php');
		}

		if ($snippet->getCodeType() === 'json') {
			file_put_contents($this->snippetsPath() . $snippet->getFileNameWithoutExtension() . '.json', $code);
		} else if ($snippet->getCodeType() === 'scssp') {
			file_put_contents($this->snippetsPath() . $snippet->getFileNameWithoutExtension() . '.scss', $snippet->getOriginalCode());
		} else {
			if ($snippet->getRenderType() !== 'none') {
				file_put_contents($this->snippetsPath() . $snippet->getMainFileName(), $code);
			}
		}

		$this->signSnippet($snippetId);

		// Don't add snippets to the main plugin file
		if (!$snippet->shouldAddToMainPluginFile()) {
			return false;
		}

		$this->addCodeToMainPluginFile($snippet, $enabled);

		return true;
	}

	function deleteSnippet($snippetId)
	{
		if (!$this->isEnabled()) {
			return;
		}

		$snippet = $this->createInternalSnippet($snippetId);

		$fileName = $snippet->getMainFileName();

		$mainFileContent = file_get_contents($this->mainFile);
		$mainFileContent = str_replace($this->getSnippetFileNameDisabled($fileName) . "\n", '', $mainFileContent);
		$mainFileContent = str_replace($this->getSnippetFileName($fileName) . "\n", '', $mainFileContent);
		file_put_contents($this->mainFile, $mainFileContent);

		$files = $snippet->getFiles();

		foreach ($files as $file) {
			@unlink($this->path . $file);
		}
	}

	private function getSnippetFileName($fileName)
	{
		return "include_once 'snippets/$fileName';";
	}

	private function getSnippetFileNameDisabled($fileName)
	{
		return "// include_once 'snippets/$fileName';";
	}

	private function copySnippets()
	{
		global $wpdb;

		$snippetRepository = new \Wpcb2\Repository\SnippetRepository();
		$snippets = $snippetRepository->getAllSnippetsQuery();

		foreach ($snippets as $snippet) {
			$this->saveSnippet($snippet['id']);

		}
	}

	/**
	 * @param string $fileName
	 * @param $mainFileContent
	 * @param bool $enabled
	 * @return void
	 */
	public function addCodeToMainPluginFile(Snippet $snippet, $enabled)
	{

		$mainFileContent = file_get_contents($this->mainFile);
		$fileName = $snippet->getMainFileName();

		$mainFileContent = str_replace($this->getSnippetFileNameDisabled($fileName) . "\n", '', $mainFileContent);
		$mainFileContent = str_replace($this->getSnippetFileName($fileName) . "\n", '', $mainFileContent);

		if ($enabled) {
			$mainFileContent = str_replace('// Snippets will go before this line, do not edit', "" . $this->getSnippetFileName($fileName) . "\n// Snippets will go before this line, do not edit", $mainFileContent);

		} else {
			$mainFileContent = str_replace('// Snippets will go before this line, do not edit', "" . $this->getSnippetFileNameDisabled($fileName) . "\n// Snippets will go before this line, do not edit", $mainFileContent);

		}

		file_put_contents($this->mainFile, $mainFileContent);
	}


	public function updateSnippet($snippet)
	{

		if (!$this->isEnabled()) {
			return false;
		}

		$this->deleteSnippet($snippet['id']);
		$this->saveSnippet($snippet['id']);

		return true;
	}

	public function createFolder($name)
	{
		if (!$this->isEnabled()) {
			return false;
		}

		$name = $this->slugifier->slugify($name);

		if (!is_dir($this->snippetsPath() . $name)) {
			mkdir($this->snippetsPath() . $name);
			touch($this->snippetsPath() . $name . '/index.php');
		}

		return true;
	}

	public function renameFolder($oldName, $newName)
	{

		if (!$this->isEnabled()) {
			return false;
		}

		$newName = $this->slugifier->slugify($newName);

		@rename($this->assetsPath() . '/css/' . $oldName, $this->assetsPath() . '/css/' . $newName);
		@rename($this->assetsPath() . '/css/' . $oldName, $this->assetsPath() . '/css/' . $newName);
		@rename($this->snippetsPath() . $oldName, $this->path . 'snippets/' . $newName);

		return true;
	}

	function deleteFolder($folderName)
	{

		if (!$this->isEnabled()) {
			return false;
		}

		@unlink($this->snippetsPath() . $folderName . '/index.php');
		@rmdir($this->snippetsPath() . $this->slugifier->slugify($folderName));
		@unlink($this->assetsPath() . '/css/' . $folderName . '/index.php');
		@unlink($this->assetsPath() . '/js/' . $folderName . '/index.php');
		@rmdir($this->assetsPath() . '/css/' . $this->slugifier->slugify($folderName));
		@rmdir($this->assetsPath() . '/js/' . $this->slugifier->slugify($folderName));

		return true;
	}

	public function getSnippetPath($snippet)
	{
		$snippet = $this->createInternalSnippet($snippet['id']);

		if ($snippet->isAsset()) {
			return $this->assetsPath() . $snippet->getFileExtension() . '/' . $snippet->getFileName();
		}

		return $this->snippetsPath() . $snippet->getFileName();
	}

	public function createInternalSnippet($snippetId)
	{

		$snippet = $this->snippetRepository->getSnippet($snippetId);
		$snippetFactory = new SnippetFactory(new GlobalCSS(), new GlobalJS(), $snippet);
		return $snippetFactory->createInternalSnippet(true);
	}

	public function slugify($string)
	{
		return $this->slugifier->slugify($string);
	}

	public function signSnippet($snippetId)
	{
		if(!$this->isEnabled()) {
			return;
		}

		if($this->isTemp) {
			return;
		}

		$snippet = $this->snippetRepository->getSnippet($snippetId);
		$snippetFactory = new SnippetFactory(new GlobalCSS(), new GlobalJS(), $snippet);

		$internalSnippet = $snippetFactory->createInternalSnippet(true);

		if(file_exists($this->path . 'signatures.php') === false) {
			file_put_contents($this->path . 'signatures.php', "<?php die; ?>\n" . json_encode([]));
		}

		$signaturesContent = file_get_contents($this->path . 'signatures.php');
		$signaturesContent = str_replace("<?php die; ?>\n", '', $signaturesContent);

		$signatures = json_decode($signaturesContent, true);

		$signatures[$internalSnippet->getId()] = $internalSnippet->getSignature();

		file_put_contents($this->path . 'signatures.php', "<?php die; ?>\n" . json_encode($signatures, JSON_PRETTY_PRINT));
	}

	public function checkSnippetSignatures()
	{
		if(!$this->isEnabled()) {
			return [];
		}

		$notMatchingSnippets = [];

		$signaturesContent = file_get_contents($this->path . 'signatures.php');
		$signaturesContent = str_replace("<?php die; ?>\n", '', $signaturesContent);


		$signatures = json_decode($signaturesContent, true);

		$snippetRepository = new SnippetRepository();
		$snippets = $snippetRepository->getAllSnippetsQuery();
		foreach ($snippets as $snippet) {

			$snippet = $snippetRepository->getSnippet($snippet['id']);
			$snippetFactory = new SnippetFactory(new GlobalCSS(), new GlobalJS(), $snippet);
			$internalSnippet = $snippetFactory->createInternalSnippet(true);

			if($signatures[$internalSnippet->getId()] !== $internalSnippet->getSignature()) {
				$notMatchingSnippets[] = $internalSnippet->getId();
			}

			if(!isset($signatures[$internalSnippet->getId()])) {
				$notMatchingSnippets[] = $internalSnippet->getId();
			}
		}

		return $notMatchingSnippets;
	}
}
