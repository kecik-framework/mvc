**Kecik Database**
================
Merupakan pustaka/library yang dibuat khusus Framework Kecik, pustaka/library ini dibuat sebagai MVC external yg akan secara otomatis menggantikan fungsi dari MVC internal yang hanya men-generate perintah SQL, tapi pustaka ini akan menjalankan fungsi Model sebagaimana mestinya.

## **Cara Installasi**
file composer.json
```json
{
	"require": {
		"kecik/kecik": "1.0.*@dev",
		"kecik/database": "1.0.*@dev"
		"kecik/mvc": "1.0.*@dev"
	}
}
```

Jalankan perintah
```shell
composer install
```

## **Cara Menggunakannya**
Untuk kebutuhan assets anda bisa download di http://getbootstrap.com/ untuk Bootstrap.

buat struktur direktori seperti ini:
```
+--app
|  +-- controllers
|  +-- models
|  +-- views
+-- assets
|   +-- css
|   +-- js
|   +-- images
+-- templates
```
Simpan file **`bootstrap.min.css`** dan  **`bootstrap-theme.min.css`** kedalam direktori **`assets/css/`**, lalu buat file **`starter-template.css`** dengan isi file sebagai berikut:
```css
body {
  padding-top: 50px;
}
.starter-template {
  padding: 40px 15px;
  text-align: center;
}
```

Lalu simpan juga kedalam direktori **`assets/css`**. 

Lalu buat file **`composer.json`** dengan isi berikut ini:
```json
{
	"require": {
		"kecik/kecik": "1.0.*@dev",
		"kecik/dic": "1.0.*@dev",
		"kecik/database": "1.0.*@dev",
		"kecik/mvc": "1.0.*@dev"
	}
}
```

Jalankan perintah
```shell
composer install
```

Untuk Database pada contoh ini menggunakan database **mysql**.
Selanjutnya buat database dengan nama database **`kecik`**, lalu jalankan perintah sql berikut ini:
```sql
CREATE TABLE IF NOT EXISTS `user` (
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `user` (`username`, `password`, `fullname`, `email`) VALUES
('admin', '21232f297a57a5a743894a0e4a801fc3', 'Administrator', 'dna.extrim@gmail.com'),
('kecik', '9981b95fcb28eddb5a4dcab5fbe71061', 'Kecik User', 'kecik@blabla.com');
```
Selanjutnya buat file **`index.php`** dengan isi sebagai berikut:
```php
<?php
require "vendor/autoload.php";

$config = [
	//** Path of Assets
	'path.assets'   => 'assets',
	//** Path of MVC
	'path.mvc'      => 'app',
	//** Path of Template
	'path.template' => 'templates',
	
	//** Load Libraries
	'libraries' => [
		//-- DIC Library
		'DIC' => ['enable' => TRUE],
		//-- Database Library
		'Database' => [
			'enable' => TRUE,
			'config' => [
				'driver' => 'mysqli',
				'hostname' => 'localhost',
				'username' => 'root',
				'password' => '',
				'dbname' => 'kecik'
			]
		],
		//-- MVC Library
		'MVC' => ['enable' => TRUE]
	]
	//-- End Libraries
];

$app = new Kecik\Kecik($config);

	//** Assets
	//-- CSS Assets
	$app->assets->css->add('bootstrap.min');
	$app->assets->css->add('bootstrap-theme.min');
	$app->assets->css->add('starter-template');
	//-- END CSS Assets
	//-- END Assets

	//** Connect to Database
	$app->db->connect();

	//** DIC Container
	//-- User Controller
	$app->container['userController'] = function($container) use ($app) {
		return new Controller\User($app);
	};
	//-- END
	
	//** Index
	$app->get('/', function() use ($app) {
		$app->container['userController']->index();
	})->template('bootstrap_template');
	
	//** User List
	$app->get('user', function() use ($app) {
		$app->container['userController']->read();
	})->template('bootstrap_template');

	//** FORM
	//-- FORM Add
	$app->get('add', function() use ($app) {
		$app->container['userController']->form();
	})->template('bootstrap_template');
	//-- FORM Update
	$app->get('edit/:username', function($username) use ($app) {
		$app->container['userController']->form($username);
	})->template('bootstrap_template');
	//-- END FORM

	//** Action INSERT, UPDATE, AND DELETE
	//-- INSERT
	$app->post('insert', function() use ($app) {
		$app->container['userController']->insert();
	});
	//-- UPDATE
	$app->post('update/:username', function($username) use ($app) {
		$app->container['userController']->update($username);
	});
	//-- DELETE
	$app->get('delete/:username', function($username) use ($app) {
		$app->container['userController']->delete($username);
	});
	//-- END Action

$app->run();
```

Untuk template ketikan code berikut ini, nama file adalah **`templates/bootstrap_template.php`**:

```html
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->  
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->  
<!--[if !IE]><!--> <html> <!--<![endif]-->  
    <head>
        <title>Contoh MVC</title>
        
        <meta charset="utf-8">
		<title>Simple Template</title>

		<meta name="description" content="overview &amp; stats" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		
		@css
	</head>
	<body>
		<nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Kecik Framework</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="{{ $this->url->to('') }}">Home</a></li>
            <li><a href="{{ $this->url->to('data') }}">User</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container">

        @controller

    </div><!-- /.container -->
		

		@js
	</body>
</html>
```

Selanjutnya buat file controller, dengan nama file **`user.php`** pada direktori **`app/controllers/`** dengan isi file sebagai berikut:

```php
<?php
namespace Controller;

use Kecik\Controller;

class User extends Controller {
	var $app = NULL;

	public function __construct(\Kecik\Kecik $app) {
		parent::__construct();
		$this->app = $app;
	}

	public function index() {
		$this->view('index');
	}

	public function read() {
		$this->view('read');
	}

	public function form($id='') {
		if ($id=='') 
			$url = $this->app->url->linkto('insert');
		else
			$url = $this->app->url->linkto('update/'.$id);

		$this->view('form', ['id'=>$id, 'url'=>$url]);
	}

	public function insert() {
		$input = $this->app->input;
		$user = new \Model\User();
			$user->username = $input->post('username');
			$user->password = md5($input->post('password'));
			$user->fullname = ucwords($input->post('fullname'));
			$user->email = $input->post('email');
		$user->save();
		$this->app->url->redirect('user');
	}

	public function update($id) {
		$input = $this->app->input;
		$user = new \Model\User(['username'=>$id]);
			$user->username = $input->post('username');
			$user->password = md5($input->post('password'));
			$user->fullname = ucwords($input->post('fullname'));
			$user->email = $input->post('email');
		$user->save();
		$this->app->url->redirect('user');
	}

	public function delete($id) {
		$input = $this->app->input;
		$user = new \Model\User(['username' => $id]);
		$user->delete();
		$this->app->url->redirect('user');
	}
}
```

Selanjutnya buat file model dengan nama file **`user.php`** dan simpan pada direktori **`app/models/`** dengan isi file sebagai berikut:

```php
<?php
namespace Model;

use Kecik\Model;

class User extends Model {
	protected static $table = 'user';

	public function __construct($id='') {
		parent::__construct($id);
	}
}
```

Selanjutnya buat bagian views untuk tampilan index, form, dan read. Pertama kita buat view untuk index dengan membuat nama file **`index.php`** pada direktori **`app/views/`** dengan isi file sebagai berikut:

```html
<div class="starter-template">
	<h2>Framework Kecik</h2>
	<p align="justify">
		Merupakan framework dengan satu file system yang sangat sederhana, jadi ini bukan merupakan sebuah framework yang 
		kompleks, tapi anda dapat membangun dan mengembangkan framework ini untuk menjadi sebuah framework yang kompleks.
		Framework ini mendukung MVC sederhana dimana anda masih harus mengcustom beberapa code untuk mendapatkan MVC yang
		kompleks, untuk Model hanya sebatas men-generate perintah SQL untuk INSERT, UPDATE dan DELETE saja, jadi untuk 
		code pengeksekusian SQL nya tersebut silakan dibuat sendiri dengan bebas mau menggunakan library database manapun.
		Framework ini juga mendukung Composer, jadi bisa memudahkan anda untuk menambahkan sebuah library dari composer.
	</p>

	<h1>EXAMPLE/CONTOH MVC</h1>
</div>
```

Lalu buat file view untuk read dengan nama file **`read.php`** dan disimpan pada direktori **`app/views/`** dengan isi file sebagai berikut:

```html
<br />
<a href="<?php $this->app->url->to('index.php/add') ?>" class="btn btn-success">Add Data</a><br />

<table class="table table-striped table-hover">
	<thead>
		<tr>
			<th>NO</th>
			<th>USERNAME</th>
			<th>EMAIL</th>
			<th>ACTION</th>
		</tr>
	</thead>

	<tbody>
		<?php
		$rows = Model\User::find();
		$no = 1;
		foreach ($rows as $data) {
		?>
		<tr>
			<td><?php echo $no; ?></td>
			<td><?php echo $data->username; ?></td>
			<td><?php echo $data->email; ?></td>
			<td>
				<a href="<?php $this->app->url->to('index.php/edit/'.$data->username) ?>" class="btn btn-primary">UPDATE</a>
				<a href="<?php $this->app->url->to('index.php/delete/'.$data->username) ?>" class="btn btn-danger">DELETE</a>
			</td>
		</tr>
		<?php
			$no++;
		}
		?>
	</tbody>
</table>
```

Lalu buat view untuk form dengan nama file **`form.php`** dan di simpan pada direktori **`app/views`** dengan isi file sebagai berikut:

```html
<?php
  if ($id != '') {
    $rows = Model\User::find([
      'where' => [
        ['username', '=', "'$id'"]
      ]
    ]);
    foreach ($rows as $data) {
      $username = $data->username;
      $fullname = $data->fullname;
      $email = $data->email;
    }
  }
?>
<br />
<form method="POST" action="<?php echo $url ?>">
  <div class="form-group">
    <label for="username">Username</label>
    <input type="text" class="form-control" id="username" name="username" placeholder="Input Username" value="<?php echo (isset($username))? $username:''; ?>" />
  </div>
  <div class="form-group">
    <label for="password">Password</label>
    <input type="password" class="form-control" id="password" name="password" placeholder="Input Password" value="" />
  </div>
  <div class="form-group">
    <label for="fullname">Fullname</label>
    <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Input Fullname" value="<?php echo (isset($fullname))? $fullname:''; ?>" />
  </div>
  <div class="form-group">
    <label for="email">Email</label>
    <input type="email" class="form-control" id="email" name="email" placeholder="Input Email" value="<?php echo (isset($email))? $email:''; ?>" />
  </div>

  <button type="submit" class="btn btn-default">Save</button>
</form>
```
