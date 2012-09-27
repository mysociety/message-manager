<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

$cakeDescription = __d('cake_dev', 'CakePHP: the rapid development php framework');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		MessageManager:
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css('cake.generic');
		echo $this->Html->css('message-manager');
		
		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
</head>
<body class="<?php echo($site_css_class); ?>">
	<div id="container" >
		<div id="header">
			<h1><a href="/">Message Manager</a></h1>
			<ul class="user-details">
				<?php
					if (AuthComponent::user('id')) { ?>
						<li>
							user: <?php echo h(AuthComponent::user('username')); ?>
						</li>
						<li>
							<?php echo $this->Html->link(__('logout'), array('controller' => 'Users', 'action' => 'logout')); ?>
						</li>
				<?php } else { ?>
						<li>
							not logged in
						</li>
						<li>
							<?php echo $this->Html->link(__('login'), array('controller' => 'Users', 'action' => 'login')); ?>
						</li>
				<?php  } ?>
			</ul>
			<?php
				if (AuthComponent::user('id')) { ?>
					<div id="header-nav">
						<?php echo $this->element('navmenu'); ?>
					</div>
				<?php  } ?>
			</ul>
				    
		</div>
		<div id="content">
			<?php echo $this->Session->flash(); ?>
			<?php echo $this->Session->flash('auth'); ?>
			<?php echo $this->fetch('content'); ?>
		</div>
		<div id="footer">			
			<?php echo $this->Html->link(__('about'), "/about", array('class'=>'mm-link')); ?>
			<?php echo $this->Html->link(__('help'), "/help", array('class'=>'mm-link')); ?>
			<?php echo $this->Html->link(__('API'), array('controller' => 'pages', 'action' => 'api'), array('class'=>'mm-link')); ?>
			
			<?php echo $this->Html->link(__('mySociety'), "http://www.mysociety.org"); ?>
			
			<?php echo $this->Html->link(
					$this->Html->image('cake.power.gif', array('alt' => $cakeDescription, 'border' => '0')),
					'http://www.cakephp.org/',
					array('target' => '_blank', 'escape' => false, 'class'=>'cake-link')
				);
			?>
		</div>
	</div>
	<?php echo $this->Js->writeBuffer(); ?>
	
	<?php //echo $this->element('sql_dump'); ?>
</body>
</html>
