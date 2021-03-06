<?php

namespace MyPoll\Classes;

use Exception;
use RedBeanPHP\Facade;

/**
 * Class Settings
 *
 * @package MyPoll\Classes
 */
class Settings
{
    /** @var \Twig_Environment */
    protected $twig;

    /** @var  Login */
    protected $login;

    /** @var  int */
    protected $id;

    /** @var  string */
    protected $siteName;

    /** @var  int */
    protected $resultNumber;

    /** @var  int */
    protected $siteCookies;

    /** @var  int */
    protected $siteCache;

    /** @var int */
    protected $siteMaxAnswers;

    /** @var string */
    private $logPage = 'index.php?do=questions';

    /** @var string */
    private $indexPage = 'index.php';

    /**
     * @return string
     */
    public function getIndexPage()
    {
        return $this->indexPage;
    }

    /**
     * @return string
     */
    public function getLogPage()
    {
        return $this->logPage;
    }

    /**
     * @return int
     */
    public function getSiteCache()
    {
        return $this->siteCache;
    }

    /**
     * @return int
     */
    public function getSiteCookies()
    {
        return $this->siteCookies;
    }

    /**
     * @return int
     */
    public function getResultNumber()
    {
        return $this->resultNumber;
    }

    /**
     * @return string
     */
    public function getSiteName()
    {
        return $this->siteName;
    }

    /**
     * @return int
     */
    public function getSiteMaxAnswers()
    {
        return $this->siteMaxAnswers;
    }


    /**
     * @param Factory $factory
     * @param int     $id
     */
    public function __construct($factory, $id)
    {
        $this->twig = $factory->getTwigAdminObj();
        $this->login = $factory->getLoginObj();
        $this->id = $id;

        $settings = $this->processSettings($this->id);
        $this->setProperties($settings);
    }

    /**
     * @param  int $id
     *
     * @return boolean|Facade::load
     */
    private function checkSettingsExist($id)
    {
        $queryResult = Facade::load('settings', $id);
        if ($queryResult->isEmpty()) {
            return false;
        }
        return $queryResult;
    }

    /**
     * @param  int $id
     *
     * @return string|Facade::load
     */
    private function processSettings($id)
    {
        $settings = $this->checkSettingsExist($id);
        if (!$settings) {
            echo General::ref($this->getIndexPage());
        }
        return $settings;
    }

    /**
     * @param Facade ::load $queryResult
     *
     * @return void
     */
    private function setProperties($queryResult)
    {
        $this->siteName = $queryResult->site_name;
        $this->resultNumber = $queryResult->site_resultsnumber;
        $this->siteCookies = $queryResult->site_cookies;
        $this->siteCache = $queryResult->site_cache;
        $this->siteMaxAnswers = $queryResult->site_maxanswers;
    }

    /**
     * @return string
     */
    public function edit()
    {
        $settings = $this->processSettings($this->id);

        return $this->twig->render('settings.html', array(
            'id' => $settings->id,
            'site_name' => $settings->site_name,
            'site_resultsnumber' => $settings->site_resultsnumber,
            'site_cookies' => $settings->site_cookies,
            'site_cache' => $settings->site_cache,
            'site_maxanswers' => $settings->site_maxanswers
        ));
    }

    /**
     * @param array $settingsArr
     *
     * @return void
     */
    public function editExecute($settingsArr)
    {
        try {
            $settings = Facade::load('settings', $this->id);
            $settings->site_name = $settingsArr['site_name'];
            $settings->site_resultsnumber = $settingsArr['site_resultsnumber'];
            $settings->site_cookies = $settingsArr['site_cookies'];
            $settings->site_cache = $settingsArr['site_cache'];
            $settings->site_maxanswers = $settingsArr['site_maxanswers'];
            Facade::store($settings);

            echo "Settings edited successfully";
        } catch (Exception $e) {
            echo 'Error :' . $e->getMessage();
        }
    }

    /**
     * @return void
     */
    public function checkCache()
    {
        if ($this->getSiteCache() == 1) {
            $this->twig->setCache('../cache');
        }
    }
}
