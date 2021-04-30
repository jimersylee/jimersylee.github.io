<?php

/*
 *
 * BlogCI4 - Blog write with Codeigniter v4dev
 * @author Deathart <contact@deathart.fr>
 * @copyright Copyright (c) 2018 Deathart
 * @license https://opensource.org/licenses/MIT MIT License
 */

namespace App\Libraries;

use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Exceptions\PageNotFoundException;
use Twig\Environment;
use Twig\Error\Error;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

/**
 * Class General
 *
 * @package App\Libraries
 */
class Twig
{
    /**
     * @var Environment
     */
    private $environment;

    /**
     * @var string
     */
    private $ext = '.twig';

    /**
     * Twig constructor.
     *
     * @param string $templateFolder
     * @throws DatabaseException
     */
    public function __construct(string $templateFolder)
    {

        $loader = new FilesystemLoader(ROOTPATH . 'public/theme' . DIRECTORY_SEPARATOR . $templateFolder);

        if (!is_writable(WRITEPATH . 'cache') || ENVIRONMENT == "development") {
            $dataConfig['cache'] = WRITEPATH . 'cache';
            $dataConfig['auto_reload'] = true;
        }

        if (ENVIRONMENT == "development") {
            $dataConfig['debug'] = true;
        }

        $dataConfig['autoescape'] = false;

        $this->environment = new Environment($loader, $dataConfig);
        //$this->environment->addExtension(new CoreExtension());
        if (ENVIRONMENT == "development") {
            $this->environment->addExtension(new DebugExtension());
        }
    }

    /**
     * @param string $file
     * @param array $array
     *
     * @return string
     */
    public function render(string $file, array $array): string
    {
        try {
            $template = $this->environment->load($file . $this->ext);
        } catch (Error $error_Loader) {
            throw new PageNotFoundException($error_Loader);
        }

        return $template->render($array);
    }
}
