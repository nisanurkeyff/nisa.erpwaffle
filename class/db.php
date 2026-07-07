<?

class DB {

	static $pdo = null;
	static $charset = 'UTF8';
	static $last_stmt = null;
	
	public static function instance()
	{
		return 
			self::$pdo == null ?
				self::init() :
				self::$pdo;
	}
	
	public static function baglan($HOST, $DB, $USER, $PASS)
	{
		self::$pdo = new PDO(
			'mysql:host=' . $HOST .';dbname=' . $DB,
			$USER,
			$PASS
		);

		self::$pdo->exec('SET NAMES `' . self::$charset . '`');
		self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

		return self::$pdo;
	}
	
	/*
	* PDO'nun query metoduna bindings
	* ilave edilmiş metot
	*/
	public static function query($query, $bindings = null)
	{
	    try {
	        $stmt = self::$pdo->prepare($query);
	        $stmt->execute($bindings);
	        return $stmt;
	    } catch (PDOException $e) {
	    	var_dump2($e->getMessage());
	    	echo "Sorgu<br><br>";
	    	var_dump2($query);
	    	echo "Parametreler<br><br>";
	        var_dump2($bindings);
	        die();
	    }
	}

	/*
	* Yapılan sorgunun ilk satırının
	* ilk değerini döndüren metod
	*/
	public static function getVar($query, $bindings = null)
	{
		if(!$stmt = self::query($query, $bindings))
			return false;

		return $stmt->fetchColumn();
	}

	/*
	* Yapılan sorgunun ilk satırını
	* döndğren metod
	*/
	public static function getRow($query, $bindings = null)
	{
		if(!$stmt = self::query($query, $bindings))
			return false;

		return $stmt->fetch();
	}

	/*
	* Yapılan sorgunun tüm satırlarını
	* döndüren metod
	*/
	public static function get($query, $bindings = null)
	{
		if(!$stmt = self::query($query, $bindings))
			return false;

		$result = array();

		foreach($stmt as $row)
			$result[] = $row;

		return $result;
	}

	/*
	* Query metodu ile aynı işlemi yapar
	* fakat etkilenen satır sayısını
	* döndürür
	*/
	public static function exec($query, $bindings = null)
	{	
		if(!$stmt = self::query($query, $bindings))
			return false;

		return $stmt->rowCount();
	}

	/*
	* Query metodu ile aynı işlemi yapar
	* fakat son eklenen ID'yi döndürür
	*/
	public static function insert($query, $bindings = null)
	{
		if(!$stmt = self::query($query, $bindings))
			return false;

		return self::$pdo->lastInsertId();
	}


	/*
	* Son gerçekleşen sorgudaki (varsa)
	* hatayı döndüren metod
	*/
	public static function getLastError()
	{
		$error_info = self::$last_stmt->errorInfo();

		if($error_info[0] == 00000)
			return false;

		return $error_info;
	}

	public static function getSQL($query = "", $bindings = array())
	{
		if(count2($bindings)==0) return $query;
		
		$query_echo = $query;
		foreach($bindings as $key => $value){
			//$query_echo = str_replace($key, "'$value'", $query_echo);
			$query_echo = preg_replace('/'.$key.'\b/', "'$value'", $query_echo);
		}
		return $query_echo;
		
	}

	/*
	* Statik olarak çağırılan ve yukarıda olmayan 
	* tüm metodları PDO'da çağıran sihirli metot
	*/
	public static function __callStatic($name, $arguments)
	{
		return call_user_func_array(
			array(self::instance(), $name),
			$arguments
		);
	}
}
