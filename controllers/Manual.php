<?php namespace Sensory5\Manual\Controllers;

use Backend;
use BackendMenu;
use Cms\Classes\Theme;
use Cms\Classes\Content as CmsContent;
use Backend\Classes\Controller;
use Sensory5\Manual\Models\Settings;

/**
 * Manual Back-end Controller
 */
class Manual extends Controller
{
    const CONTENT_DIR = 'manual/';

    /**
     * @var string Layotu to use for the view
     */
    public $bodyClass = 'compact-container';

    /**
     * @var string current content
     */
    public $currentContent = '';

    /**
     * @var string current page id
     */
    public $currentContentId = '';

    /**
     * @var string current header title
     */
    public $currentHeaderTitle = '';

    /**
     * @var string theme
     */
    public $theme = null;

    public function __construct()
    {
        parent::__construct();

        $cssFile = Settings::get('sensory5.manual::css_path', 'plugin');
        $cssFile = $cssFile == 'plugin' ?  
          '/plugins/sensory5/manual/assets/css/manual.css' : 
          '/themes/ . '$this->getManualTheme() . '/content/manual.css';
        $this->addCss($cssFile);

        BackendMenu::setContext('Sensory5.Manual', 'site', 'manual');
    }

    /**
     * Renders the default home page for the manual
     */
    public function index()
    {

        $this->pageTitle = 'Site Manual';

        try {
            $content = CmsContent::load($this->getManualTheme(), self::CONTENT_DIR.'index.md');

            if ($content) {
                $this->currentContentId = null;
                $this->currentContent = $content->parseMarkup();
            }
            else {
                $this->currentContent = "";
            }
        }
        catch(Exception $e) {
            \ApplicationException($e->getMessage());
        }

    }

    /**
     * Renders the page view
     */
    public function view($contentId)
    {

        try {

            $content = CmsContent::load($this->getManualTheme(), self::CONTENT_DIR.$contentId.'.md');

            $this->pageTitle = ucwords(str_replace("-", " ", $contentId));

            $this->currentContentId = $contentId;

            if (! $content) {
                return \Redirect::to('404');
            }
            else {
                $this->currentContent = $content->parseMarkup();
            }

        }
        catch(Exception $e) {
            \ApplicationException($e->getMessage());
        }

    }

    /**
     * Renders the current section header in the menu
     */
    public function getCurrentHeader($item) {
        if (!empty($item->section) && $item->section !== $this->currentHeaderTitle) {
            $this->currentHeaderTitle = $item->section;
            return $item->section;
        }
        return "";
    }

    /**
     * Renders the side bar menu
     */
    protected function renderMenu() {

        $this->currentHeaderTitle = '';

        $self = $this;

        $contents = CmsContent::listInTheme($this->getManualTheme(), true)->reduce(function($result, $item) use($self) {

            $file = $item->fileName;

            if (starts_with($file, self::CONTENT_DIR) &&
                $file !== self::CONTENT_DIR.'index.md') {

                $content = new \StdClass;

                $file = str_replace(".md", "", substr($file, strlen(self::CONTENT_DIR)));
                $content->id = $file;

                // Split on underscores for section titles
                $splits = explode("_", $file);

                // Remove ordering number
                $content->order = intval(strstr($splits[0], "-", true));

                $title = "";
                $section = "";
                if (count($splits) > 1) {
                    $section = strstr($splits[0], "-", false);
                    $section = ucwords(str_replace("-", " ", $section));
                    $title = ucwords(str_replace("-", " ", $splits[1]));
                } else {
                    $title = ucwords(str_replace("-", " ", strstr($splits[0], "-", false)));
                }

                $content->section = $section;
                $content->title = $title;
                $content->link = Backend::url('sensory5/manual/manual/view/'.$file);
                $result[] = $content;

            }
            return $result;

        }, []);

        // sort contents by order id
        usort($contents, function($a, $b) {
            return ($a->order < $b->order) ? -1 : (($a->order === $b->order) ? 0 : 1);
        });

        return $this->makePartial('menu', ['contents' => $contents]);

    }

    /**
     * Retrieve the proper theme that contains the manual content
     */
    private function getManualTheme() {
      if (!is_null($this->theme)) {
        $this->theme = Settings::get('sensory5.manual::manual_theme', Theme::getActiveThemeCode());
      }
      return $this->theme;
    }
}
