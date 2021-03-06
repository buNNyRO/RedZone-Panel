<?php



use Illuminate\Database\Eloquent\Model;



class user extends Model {

    protected $table = 'users';

    protected $primaryKey = 'id';

	protected $hidden = ['password'];

	protected static $__user = [];

	public static $permissions = [];

	public static function init() {

		if(self::isLogged()) {

			$u = user::find(user::get());

			if(!$u) return redirect::to('logout');

			self::$__user = $u;

		}

	}



	public static function isLogged() {

		return (isset($_SESSION['user']) ? true : false);

	}



	public static function get() {

		return (isset($_SESSION['user']) ? $_SESSION['user'] : false);

	}



	public static function format($id, $name = null, $faction = null,$hover = true) {

		return self::formatName($id, $name, $faction, $hover);

	}



	public static function getData($id,$data) {

		if(!is_array($data)) {

			$q = connect::prepare('SELECT `'.$data.'` FROM `users` WHERE `id` = ?');

			$q->execute(array($id));

			$fdata = $q->fetch();

			return $fdata[$data];

		} else {

			$q = '';

			foreach($data as $d) {

				if(end($data) !== $d) $q .= '`'.$d.'`,';

				else $q .= '`'.$d.'`';

			}

			$q = connect::prepare('SELECT '.$q.' FROM `users` WHERE `id` = ?');

			$q->execute(array($id));

			return $q->fetch(PDO::FETCH_ASSOC);

		}

	}



	public static function getSkin($char, $model) {

		if(!$char) return $model;

		return $char;

	}



	public static function getSpecificData($data,$data2,$id1,$id) {

		if(!is_array($data)) {

			$q = connect::prepare('SELECT `'.$data.'` FROM `'.$data2.'` WHERE `'.$id1.'` = ?');

			$q->execute(array($id));

			$fdata = $q->fetch();

			return $fdata[$data];

		} else {

			$q = '';

			foreach($data as $d) {

				if(end($data) !== $d) $q .= '`'.$d.'`,';

				else $q .= '`'.$d.'`';

			}

			$q = connect::prepare('SELECT '.$q.' FROM `'.$data2.'` WHERE `'.$id1.'` = ?');

			$q->execute(array($id));

			return $q->fetch(PDO::FETCH_ASSOC);

		}

	}



	public static function getSpecificDatabyName($data,$data2,$id1,$id) {

		if(!is_array($data)) {

			$q = connect::prepare('SELECT `'.$data.'` FROM `'.$data2.'` WHERE `'.$id1.'` LIKE ?');

			$q->execute(array('%'.$id.'%'));

			$fdata = $q->fetch();

			return $fdata[$data];

		} else {

			$q = '';

			foreach($data as $d) {

				if(end($data) !== $d) $q .= '`'.$d.'`,';

				else $q .= '`'.$d.'`';

			}

			$q = connect::prepare('SELECT '.$q.' FROM `'.$data2.'` WHERE `'.$id1.'` = ?');

			$q->execute(array($id));

			return $q->fetch(PDO::FETCH_ASSOC);

		}

	}



	public static function getLocation($ip) {

		$data = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip));

		return $data['geoplugin_countryName'] . ', ' . $data['geoplugin_city'];

	}

}



