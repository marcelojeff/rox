<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Rox for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Rox;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Rox\View\Helper\FlashMessages;
use Rox\Hydrator\MagicMethods;
use PhlyMongo\MongoConnectionFactory;
use Rox\View\Helper\LoggedUser;
use Zend\Session\Container;

class Module implements AutoloaderProviderInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php'
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    // if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/', __NAMESPACE__)
                )
            )
        );
    }
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'magic-methods' => function ($sm){
                    return new MagicMethods;
                },
                'mongo' => function ($sm){
                	$config = $sm->get('config');
                	$config = $config['mongo'];
                	$factory = new MongoConnectionFactory($config['server'], $config['server_options']);
                	return $factory->createService($sm)->selectDB($config['db']);
                },
                'logged_user_container' => function($sm){
                	return new Container('logged_user');
                },
            )
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'flashMessages' => function ($sm)
                {
                    $plugin = $sm->getServiceLocator()
                        ->get('ControllerPluginManager')
                        ->get('flashmessenger');
                    $helper = new FlashMessages($plugin);
                    return $helper;
                },
                'loggedUser' => function ($sm){
                	return new LoggedUser($sm->getServiceLocator()->get('logged_user_container'));
                }
            ),
            
        );
    }
}
