<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Rox\Controller\DefaultController' => 'Rox\Controller\DefaultControllerController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'rox' => array(
                'type'    => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route'    => '/rox',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'Rox\Controller',
                        'controller'    => 'DefaultController',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    // This route is a sane default when developing a module;
                    // as you solidify the routes for your module, however,
                    // you may want to remove it and replace it with more
                    // specific routes.
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Rox' => __DIR__ . '/../view',
        ),
    	'template_map' => array(
    		'form-bs3-horizontal'         => __DIR__ . '/../view/partial/form/bs3/horizontal.phtml',
    	),
    ),
);
