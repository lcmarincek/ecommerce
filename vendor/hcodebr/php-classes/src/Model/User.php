<?php 

namespace Hcode\Model;

use \Hcode\Model;
use \Hcode\DB\Sql;

class User extends Model {

	const SESSION = "User";

	protected $fields = [
		"iduser", "desperson", "deslogin", "despassword", "inadmin", "dtergister","desemail","nrphone"
	];

	public static function login($login, $password):User
	{

		$db = new Sql();

		$results = $db->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(
			":LOGIN"=>$login
		));

		if (count($results) === 0) {
			throw new \Exception("Não foi possível fazer login.");
		}

		$data = $results[0];

		if (password_verify($password, $data["despassword"])) {

			$user = new User();
			$user->setData($data);

			$_SESSION[User::SESSION] = $user->getValues();

			return $user;

		} else {

			throw new \Exception("Não foi possível fazer login.");

		}

	}

	public static function logout()
	{

		$_SESSION[User::SESSION] = NULL;

	}

	public static function verifyLogin($inadmin = true)
	{

		if (
			!isset($_SESSION[User::SESSION])
			|| 
			!$_SESSION[User::SESSION]
			||
			!(int)$_SESSION[User::SESSION]["iduser"] > 0
			||
			(bool)$_SESSION[User::SESSION]["iduser"] !== $inadmin
		) {
			
			header("Location: /admin/login");
			exit;

		}

	}
	
	public static function listAll()
	{
		$sql = new Sql();
		return $sql->select("select * from tb_users a INNER join tb_persons b using(idperson) order by b.desperson;");
	}
	
	
	public function save()
	{
		$sql = new Sql();
/*
pdesperson VARCHAR(64), 
pdeslogin VARCHAR(64), 
pdespassword VARCHAR(256), 
pdesemail VARCHAR(128), 
pnrphone BIGINT, 
pinadmin TINYINT
*/

		$result = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :desnrphone, :inadmin)", array(
		":desperson"=>$this->getdesperson(),
		":deslogin"=>$this->getdeslogin(),
		":despassword"=>$this->getdespassword(),
		":desemail"=>$this->getdesemail(),
		":desnrphone"=>$this->getnrphone(),
		":inadmin"=>$this->getinadmin()
		));
		
		$this->setData($result[0]);
	}
	
	
	public function get($iduser)
	{
		$sql = new Sql();
		$results = $sql->select("Select * from tb_users a inner join tb_persons b using(idperson) where a.iduser = :iduser",array(":iduser"=>$iduser));
        $this->setData($results[0]);

	}
	
	
	public function update()
	{
		$sql = new Sql();
/*
pdesperson VARCHAR(64), 
pdeslogin VARCHAR(64), 
pdespassword VARCHAR(256), 
pdesemail VARCHAR(128), 
pnrphone BIGINT, 
pinadmin TINYINT
*/

		$result = $sql->select("CALL sp_usersupdate_save(:iduser,:desperson, :deslogin, :despassword, :desemail, :desnrphone, :inadmin)", array(
		":iduser"=>$this->getiduser(),
		":desperson"=>$this->getdesperson(),
		":deslogin"=>$this->getdeslogin(),
		":despassword"=>$this->getdespassword(),
		":desemail"=>$this->getdesemail(),
		":desnrphone"=>$this->getnrphone(),
		":inadmin"=>$this->getinadmin()
		));
		
		$this->setData($result[0]);
	}
	
	
	public function delete()
	{
		$sql = new Sql();
		
		$sql->query("CALL sp_users_delete(:iduser)",array(":iduser"=>$this->getiduser()));
		
	}
}

 ?>