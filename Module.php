<?php

/**
 * MarceloJeff Rox
 *
 * @link      http://github.com/marcelojeff/rox
 * @copyright Copyright (c) 2013 Marcelo Araújo
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
use Everyman\Neo4j\Client;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole;
use Zend\Permissions\Acl\Resource\GenericResource;
use Zend\View\Helper\FlashMessenger;
use Rox\View\Helper\Menu;

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

    public function initAcl(MvcEvent $e)
    {
        $acl = new Acl();
        $config = $e->getApplication()
            ->getServiceManager()
            ->get('config')['acl'];
        foreach ($config['roles'] as $role => $parents) {
            $acl->addRole(new GenericRole($role), $parents);
        }
        foreach ($config['resources'] as $resource => $permissions) {
            $acl->addResource(new GenericResource($resource));
            foreach ($permissions as $action => $roles) {
                foreach ($roles as $role => $privileges) {
                    $acl->$action($role, $resource, $privileges);
                }
            }
        }
        $e->getViewModel()->acl = $acl;
    }

    public function getResource($mvcEvent)
    {
        $route = $mvcEvent->getRouteMatch()->getMatchedRouteName();
        $controller = $mvcEvent->getRouteMatch()->getParam('__CONTROLLER__');
        $route = explode('/', $route);
        return sprintf('%s/%s', $route[0], strtolower($controller));
    }

    public function checkAcl(MvcEvent $e)
    {
        $currentUrl = $e->getRequest()->getUriString();
        $action = $e->getRouteMatch()->getParam('action');
        $resource = $this->getResource($e);
        $session = $e->getApplication()
            ->getServiceManager()
            ->get('logged_user_container');
        if ($session->type) {
            $userRole = $session->type;
        } else {
            $userRole = 'guest';
        }
        $acl = $e->getViewModel()->acl;        
        if (! $acl->hasResource($resource) || ! $acl->isAllowed($userRole, $resource, $action)) {
        	$message = sprintf('Você não tem permissão para acessar a página atual.');
        	$sm = $e->getApplication()->getServiceManager();
        	$flash = $sm->get('ControllerPluginManager')->get('flashMessenger');
        	$flash->addInfoMessage($message);
            $url = $e->getRouter()->assemble([
                'action' => 'login'
            ], [
                'name' => 'user/default'
            ]);
            $urlContainer = new Container('url');
            $urlContainer->savedUrl = $currentUrl;
            $response = $e->getResponse();
            $response->getHeaders()->addHeaderLine('Location', $url);
            $response->setStatusCode(302);
            $response->sendHeaders();
            exit();
        }
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'magic-methods' => function ($sm)
                {
                    return new MagicMethods();
                },
                'neo4j' => function ($sm)
                {
                    return new Client();
                },
                'mongo' => function ($sm)
                {
                    $config = $sm->get('config');
                    $config = $config['mongo'];
                    $factory = new MongoConnectionFactory($config['server'], $config['server_options']);
                    return $factory->createService($sm)->selectDB($config['db']);
                },
                'logged_user_container' => function ($sm)
                {
                    return new Container('logged_user');
                }
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
                'loggedUser' => function ($sm)
                {
                    return new LoggedUser($sm->getServiceLocator()->get('logged_user_container'));
                },
                'menu_builder' => function ($sm)
                {
                	$config = $sm->getServiceLocator()->get('config');
                	$config = $config['menu'];
                	return new Menu($config);
                },
            )
            ,
            'invokables' => [
                'simpleFormRow' => 'Rox\View\Helper\SimpleFormRow',
                'compoundFormRow' => 'Rox\View\Helper\CompoundFormRow',
                'renderFieldsets' => 'Rox\View\Helper\RenderFieldsets',
                'inlineCheckbox' => 'Rox\View\Helper\InlineCheckbox'
            ]
        );
    }
}
