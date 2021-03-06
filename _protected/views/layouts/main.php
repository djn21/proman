<?php
	/* @var $this \yii\web\View */
	/* @var $content string */

	use app\assets\AppAsset;
	use app\widgets\Alert;
	use yii\helpers\Html;
	use yii\widgets\Breadcrumbs;
	use yii\controllers\SiteController;
	use app\controllers\MessageController;
	use app\controllers\ProfileController;
	use app\controllers\ProjectProfileController;
	use app\controllers\TaskProfileController;
	use app\controllers\ActivityProfileController;
	use app\controllers\UserController;
	use app\controllers\TaskController;
	use app\controllers\ProjectController;

	AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
	<head>
		<meta charset="<?= Yii::$app->charset ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<?= Html::csrfMetaTags() ?>
		<title><?= Html::encode($this->title) ?></title>
		<link rel="icon" href='<?= Yii::$app->request->BaseUrl ?>/uploads/logo.png'>
		<link href='https://fonts.googleapis.com/css?family=Ubuntu:400,700' rel='stylesheet' type='text/css'>
		<?php $this->head() ?>
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
		<?php $this->beginBody() ?>
		<div class="wrapper">
	  		<header class="main-header">
				<!-- Logo -->
				<a href='<?= Yii::$app->request->BaseUrl ?>' class="logo">
					<!-- mini logo for sidebar mini 50x50 pixels -->
					<span class="logo-mini"><b>PR</b>M</span>
		  			<!-- logo for regular state and mobile devices -->
		  			<span class="logo-lg"><b>PROJECT</b>MANAGER</span>
				</a>
				<!-- Header Navbar: style can be found in header.less -->
				<nav class="navbar navbar-static-top">
					<!-- Sidebar toggle button-->
     				<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        				<span class="sr-only">Toggle navigation</span>
      				</a>
				  	<div class="navbar-custom-menu">
						<ul class="nav navbar-nav">
					  		<!-- Messages: style can be found in dropdown.less-->
					  		<?php
					  			$baseUrl=Yii::$app->request->BaseUrl;
					  			if(!Yii::$app->user->isGuest){
					  				//don't have profile
						  			if(($user=ProfileController::profileByUserId(Yii::$app->user->id))==null){
						  				$userName=Yii::$app->user->identity->username;
						  				$userImageUrl=$baseUrl . '/uploads/0.png';
						  				$userProfile='create';
						  			//have profile
						  			}else{
						  				$userName=$user['name'];
						  				$userImageUrl=$baseUrl . "/" . $user['image'];
						  				$userProfile='view?id=' . $user['id'];	
						  			}
					  			}else{
					  				$userName="Guest";
					  			}
					  			if(!Yii::$app->user->isGuest){
					  				$newMessages=MessageController::getMessages(Yii::$app->user->identity->email);
					  				$numberOfNewMessages=count(MessageController::newMessages(Yii::$app->user->identity->email));
					  				$numberOfActivities=ActivityProfileController::numberOfActivitiesByUserId($user['id']);
					  				echo
					  				//messages notification
							  		"<li class='dropdown messages-menu'>
										<a href='#' class='dropdown-toggle' data-toggle='dropdown'>
								  			<i class='fa fa-envelope-o'></i>
								  			<span class='label label-success'>$numberOfNewMessages</span>
										</a>
										<ul class='dropdown-menu' style='width: 300px'>
              								<li class='header'>You have $numberOfNewMessages new messages</li>
              								<li>
	                							<ul class='menu'>";
			    								foreach($newMessages as $message) {
			    									$messageSubject=$message['subject'];
			    									$messageTime=substr($message['time'],2,14);
			    									$messageSenderId=UserController::userIdByEmail($message['email_from']);
			    									$messageSender=ProfileController::profileByUserId($messageSenderId);
			    									$senderUserName=$messageSender['name'];
			    									$senderUserImage=$baseUrl . '/' . $messageSender['image'];
			    									$href=$baseUrl . '/message/view?id=' . $message['id'];
			    									if($message['readed']){
			    										$small="<small><i class='fa fa-clock-o'></i> $messageTime</small>";
			    									}else{
			    										$small="<small class='label pull-right bg-green'>new</small>";
			    									}
			        								echo"
							                  		<li>
							                    		<a href=$href>
							                      			<div class='pull-left'>
							                        			<img src=$senderUserImage class='img-circle' alt='User Image'>
							                      			</div>
							                      			<h4>
							                        			$senderUserName
							                        			$small
							                      			</h4>
							                      			<p>$messageSubject</p>
							                    		</a>
							                  		</li>";
							                  	}
							                  	echo 
							                  	"</ul>
						                	</li>
						                	<li class='footer' style='padding: 0px; height: 30px;'><a href='$baseUrl/message/index''>See All Messages</a></li>
						                </ul>
					 				</li>";
					 				//notifications
					 				$numberOfNotification=TaskController::numberOfnotifications($user['id'])+ProjectController::numberOfnotifications($user['id']);
					 				echo "
					 					<li class='dropdown notifications-menu'>
								            <a href='#' class='dropdown-toggle' data-toggle='dropdown'>
								              <i class='fa fa-bell-o'></i>
								              <span class='label label-warning'>$numberOfNotification</span>
								            </a>
								            <ul class='dropdown-menu' style='width: 500px'>
								            	<li class='header'>You have $numberOfNotification notifications</li>
							              		<li>
							                		<!-- inner menu: contains the actual data -->
							                		<ul class='menu'>";
							                		//project notifications
							                		$projects=ProjectProfileController::projectsByUserId($user['id']);
											        foreach ($projects as $project) {
											            $currentProject=ProjectController::findActiveProject($project);
											            $date1=date_create(date('Y-m-d'));
											            $date2=date_create($currentProject['start_date']);
											            $diff=date_diff($date1,$date2);
											            $diffDays=$diff->format("%R%a");
											            $different=$diff->format("%a");
											            $projectName=$currentProject['name'];
											            if($diffDays>0 && $diffDays<=5){
											               $href=$baseUrl . '/project/view?id=' . $currentProject['id'];
											                echo
											                "<li>
										                    	<a href=$href>
										                      		<i class='fa fa-info-circle text-green'></i> $projectName starts in $different days
										                    	</a>
										                  	</li>"; 
											            }
											            $date2=date_create($currentProject['dead_line']);
											            $diff=date_diff($date1,$date2);
											            $diffDays=$diff->format("%R%a");
											            $different=$diff->format("%a");
											            if($diffDays>0 && $diffDays<=5){
											                $href=$baseUrl . '/project/view?id=' . $currentProject['id'];
											                echo
											                "<li>
										                    	<a href=$href>
										                      		<i class='fa fa-exclamation-triangle text-yellow'></i> $projectName deadline in $different days
										                    	</a>
										                  	</li>";
											            }
											        }
											        //task notifications
							                		$tasks=TaskProfileController::tasksByUserId($user['id']);
											        foreach ($tasks as $task) {
											            $currentTask=TaskController::findActiveTask($task);
											            $date1=date_create(date('Y-m-d'));
											            $date2=date_create($currentTask['start_date']);
											            $diff=date_diff($date1,$date2);
											            $diffDays=$diff->format("%R%a");
											            $different=$diff->format("%a");
											            $taskName=$currentTask['name'];
											            $projectName=ProjectController::projectNameById($currentTask['project_id'])['name'];
											            if($diffDays>0 && $diffDays<=5){
											            	$href=$baseUrl . '/task/view?id=' . $currentTask['id'];
											                echo
											                "<li>
										                    	<a href=$href>
										                      		<i class='fa fa-info-circle text-green'></i> $taskName ($projectName) starts in $different days
										                    	</a>
										                  	</li>";
											            }
											            $date2=date_create($currentTask['dead_line']);
											            $diff=date_diff($date1,$date2);
											            $diffDays=$diff->format("%R%a");
											            $different=$diff->format("%a");
											            if($diffDays>0 && $diffDays<=5){
											            	$href=$baseUrl . '/task/view?id=' . $currentTask['id'];
											                echo
											                "<li>
										                    	<a href=$href>
										                      		<i class='fa fa-exclamation-triangle text-yellow'></i> $taskName ($projectName) deadline in $different days
										                    	</a>
										                  	</li>";
									                  	}
											        }
								                  		
								                 	echo 
							                		"</ul>
								              	</li>
								            </ul>
								        </li>
					 				";
							        //nuber of users active projects
							        $numberOfProjects=ProjectController::numberOfProjects($user['id']);
							        //number of users active tasks
							        $numberOfTasks=TaskController::numberOfTasks($user['id']);
							        echo "
			  						<li class='dropdown tasks-menu'>
										<a href='#' class='dropdown-toggle' data-toggle='dropdown'>
				  							<i class='fa fa-flag-o'></i>
				  							<span class='label label-danger'>$numberOfTasks</span>
										</a>
										<ul class='dropdown-menu' style='width: 400px'>
											<li class='header'>You have $numberOfTasks active tasks</li>
											<li>
												<ul class='menu'>";
												//tasks notification
							                  	$tasks=TaskProfileController::tasksByUserId($user['id']);
							                  	foreach ($tasks as $task) {
							                  		$currentTask=TaskController::findActiveTask($task);
							                  		$taskName=$currentTask['name'];
							                  		$taskPercentage=substr($currentTask['percentage'],0,-3);
							                  		$taskColor=TaskController::taskColor($currentTask);
							                  		$projectName=ProjectController::projectNameById($currentTask['project_id'])['name'];
							                  		if($currentTask!=null){
							                  			$currentTaskId=$currentTask['id'];
								                  		echo "
								                  		<li>
										                	<a href='$baseUrl/task/view?id=$currentTaskId'>
										                      	<h3>
										                        	$taskName ($projectName)
										                        	<small class='pull-right'>$taskPercentage %</small>
										                     	</h3>
										                      	<div class='progress xs'>
										                        	<div class='progress-bar $taskColor' style='width: $taskPercentage%' role='progressbar' aria-valuenow=$taskPercentage aria-valuemin='0' aria-valuemax='100'>
										                          		<span class='sr-only'>$taskPercentage% Complete</span>
										                        	</div>
										                      	</div>
										                    </a>
										                </li>";
									           		}
							                  	}
							                  	echo" 
								            	</ul>
							           		</li>
							            	<li class='footer' style='padding: 0px; height: 30px;'>
						                		<a href='$baseUrl/task/index'>View all tasks</a>
						            		</li>
						            	</ul>
							        </li>";
								}
							?>

		  					<!-- User Account: style can be found in dropdown.less -->
      						<?php
      							if(!Yii::$app->user->isGuest){
      								echo
      								"<li class='dropdown user user-menu'>
    									<a href='#' class='dropdown-toggle' data-toggle='dropdown'>
      										<img src=$userImageUrl class='user-image' alt='User Image'>
      										<span class='hidden-xs'>" . $userName . "</span>
  						 				</a>";
      							}
  							?>		
            					<ul class="dropdown-menu">
              						<!-- User image -->
              						<li class="user-header">
              							<?php
              								if(!Yii::$app->user->isGuest){
                								echo "<img src=$userImageUrl class='img-circle' alt='User Image'>";
                							}else{
                								echo "<img src='$baseUrl/uploads/0.png' class='user-image' alt='User Image'>";
                							}
            							?>
                						<p>
                							<?php
                								if(!Yii::$app->user->isGuest){
                 									echo $userName;
                 									$email=Yii::$app->user->identity->email;
                 									echo
                 									"<small> $email </small>";
                 								}
             								?>
                						</p>
              							<!-- Menu Footer-->
              							<?php
              								if(!Yii::$app->user->isGuest){
	              								echo 
	              								"<li class='user-footer'>
	                								<div class='pull-left'>
	                  									<a href='$baseUrl/profile/$userProfile' class='btn btn-default btn-flat'>Profile</a>
	                								</div>
	                								<div class='pull-right'>
	                  									<a href='$baseUrl/logout' class='btn btn-default btn-flat'>Logout</a>
	                								</div>
	                							</li>";
	                						}
                						?>
              						</li>
           						</ul>
         					</li>
						</ul>
	  				</div>
				</nav>
 			</header>
			<!-- Left side column. contains the logo and sidebar -->
  			<aside class="main-sidebar">
				<!-- sidebar: style can be found in sidebar.less -->
				<section class="sidebar">
	  				<!-- Sidebar user panel -->
	 				<div class="user-panel">
							<?php
								if(!Yii::$app->user->isGuest){
									echo 
									"<div class='user-panel'>
 										<div class='pull-left image'>
	  										<img src='$userImageUrl' class='img-circle' alt='User Image'>
										</div>
										<div class='pull-left info'>
											<p>" . $userName . "</p>
											<a><i class='fa fa-circle' style='color: green'></i> Online</a>	
										</div>
									</div>";
									echo 
      									"<form action='#' method='get' class='sidebar-form'>
					        				<div class='input-group'>
					          					<input type='text' name='q' class='form-control' placeholder='Search...'>
					              				<span class='input-group-btn'>
					                				<button type='submit' name='search' id='search-btn' class='btn btn-flat'><i class='fa fa-search'></i></button>
					             				</span>
					       					</div>
					     				</form>";
								}
							?>	
	  				</div>
	  				<!-- sidebar menu: : style can be found in sidebar.less -->
	  				<ul class="sidebar-menu">
	  					<li class='header'>MAIN NAVIGATION </li>
						<?php
							if(!Yii::$app->user->isGuest)
							echo
							"<li>
			  					<a href=$baseUrl>
									<i class='fa fa-home'></i>
									<span>Home</span>
			  					</a>
							</li>";
							if(Yii::$app->user->can('employee')){
								echo
								"<li>
						  			<a href='$baseUrl/project/index'>
										<i class='fa fa-cubes'></i> <span>Projects</span>
										<small class='label pull-right bg-blue'>$numberOfProjects</small>
						  			</a>
								</li>
								<li>
						  			<a href='$baseUrl/task/index'>
										<i class='fa fa-tasks'></i> <span>Tasks</span>
										<small class='label pull-right bg-red'>$numberOfTasks</small>
						  			</a>
								</li>
								<li>
						  			<a href='$baseUrl/activity/index'>
										<i class='fa fa-check-square'></i> <span>Activities</span>
										<small class='label pull-right bg-aqua'>$numberOfActivities</small>
						  			</a>
								</li>";
								
							}
							if(!Yii::$app->user->isGuest){
								echo
								"<li>
							  		<a href='$baseUrl/message/index'>
										<i class='fa fa-envelope'></i> <span>Messages</span>
										<small class='label pull-right bg-green'>$numberOfNewMessages</small>
							  		</a>
								</li>";
							}
							if (Yii::$app->user->can('admin')){
								echo 
								"<li>
							  		<a href='$baseUrl/user/index'>
										<i class='fa fa-users'></i> <span>Users</span>
							  		</a>
								</li>";
								
							}
							if (Yii::$app->user->isGuest) {
								echo 
								"<li>
									<a href='$baseUrl/login'>
										<i class='fa fa-sign-in'></i> <span>Login</span>
									</a>
								</li>
								<li>
							  		<a href='$baseUrl/signup'>
										<i class='fa fa-user-plus'></i> <span>Signup</span>
							 		</a>
								</li>
								<li>
									<a href='$baseUrl/contact'>
										<i class='fa fa-envelope'></i> <span>Contact</span>
									</a>
								</li>
								<li>
									<a href='$baseUrl/about'>
										<i class='fa fa-question-circle'></i> <span>About</span>
									</a>
								</li>";	
							}
						?>
	  				</ul>
				</section>
  			</aside>
			<!-- Content Wrapper. Contains page content -->
  			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<section>
					<?= Breadcrumbs::widget(['links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],]) ?>
				</section>
				<!-- Main content -->
				<section class="content" style="min-height: 710px; padding-top: 0px">
					<?= $content ?>
				</section>
			</div>
		</div>
		<footer class="footer">
			<div class="container">
				<p class="pull-left">&copy; <?= Yii::t('app', Yii::$app->name) ?> <?= date('Y') ?></p>
				<p class="pull-right"><?= Yii::powered() ?></p>
			</div>
		</footer>
		<?php $this->endBody() ?>
	</body>
</html>
<?php $this->endPage() ?>
