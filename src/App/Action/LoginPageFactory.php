<?php
/**
 * ISBN validation and conversion functionality
 *
 * PHP version 5
 *
 * Copyright (c) Falvey Library 2017.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category VuBib
 * @package  Code
 * @author   Falvey Library <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https:// Main Page
 */
namespace App\Action;

//use App\Entity\LoginUser;
use App\Repository\UserAuthenticationInterface;
use Interop\Config\ConfigurationTrait;
use Interop\Config\RequiresConfigId;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Db\Adapter\Adapter;

/**
 * Class Definition for LoginPageFactory.
 *
 * @category VuBib
 *
 * @author   Falvey Library <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 *
 * @link     https://
 */
class LoginPageFactory implements RequiresConfigId
{
    use ConfigurationTrait;

    public function dimensions()
    {
        return ['app'];
    }
	
	/**
	* invokes required template
	**/
    public function __invoke(ContainerInterface $container)
    {
        $router = $container->get(RouterInterface::class);
        $template = ($container->has(TemplateRendererInterface::class))
            ? $container->get(TemplateRendererInterface::class)
            : null;
        $userRepository = $container->get(UserAuthenticationInterface::class);
        $adapter = $container->get(Adapter::class);
        //$userEntity = new LoginUser();

        $authenticationOptions = $this->options($container->get('config'), 'authentication');

        return new LoginPageAction(
            $router,
            $template,
            $userRepository,
            //$userEntity,
            $authenticationOptions['default_redirect_to'], $adapter,
            $container->get(\Zend\Session\Container::class)
        );
    }
}
