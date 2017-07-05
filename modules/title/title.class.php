<?php

class titleModel extends module_model {
	public function __construct($modName) {
		parent::__construct ( $modName );
	}
    public function get_assoc_array($sql){
        $this->query ( $sql );
        $items = array ();
        while ( ($row = $this->fetchRowA ()) !== false ) {
            $items[] = $row;
        }
        return $items;
    }
	public function getNewsList($limCount) {
		$page = 1;
		$limStart = ($page - 1) * $limCount;
		$sql = 'SELECT n.*, DATE_FORMAT(`time`, \'%%d.%%m.%%Y\') as time,
			    	(SELECT COUNT(*) FROM news) as news_count
			     FROM news n
				ORDER BY n.`time` DESC';
		if ($limCount > 0) $sql.= ' LIMIT '.$limStart.','.$limCount;
		$this->query($sql);
		$collect = Array();
		while($row = $this->fetchRowA()) {
			$collect[]=$row;
		}
		return $collect;
	}
    public function getPrices() {
        $sql = 'SELECT id, km_from, km_to, km_cost FROM routes_price r';
        return $this->get_assoc_array($sql);
    }
    public function getAddPrices() {
        $sql = 'SELECT id, type, cost_route FROM routes_add_price r';
        return $this->get_assoc_array($sql);
    }
    public function createUser($name, $phone, $pin_code, $sms_id){
        $passi = md5($pin_code);
        $sql = "INSERT INTO users (name, email, login, pass, date_reg, isban, prior, title, phone, phone_mess, fixprice_inside, inkass_proc, pay_type, sms_id) 
                VALUES ('$name','','$phone','$passi',NOW(),'0','0','$name','$phone','','','','','$sms_id')";
        return $this->query($sql);
    }
}
class titleProcess extends module_process {
	public function __construct($modName) {
		global $values, $User, $LOG, $System;
		parent::__construct ( $modName );
		$this->Vals = $values;
		$this->System = $System;
		$this->modName = $modName;
		$this->User = $User;
		$this->Log = $LOG;
		$this->action = false;
		/* actionDefault - Действие по умолчанию. Должно браться из БД!!! */		$this->actionDefault = '';
		$this->actionsColl = new actionColl ();
		$this->nModel = new titleModel ( $modName );
		$sysMod = $this->nModel->getSysMod ();
		$this->sysMod = $sysMod;
		$this->mod_id = $sysMod->id;
		$this->nView = new titleView ( $this->modName, $this->sysMod );
		$this->regAction ( 'view', 'Главная страница', ACTION_GROUP );
		$this->regAction ( 'register', 'Регистрация', ACTION_PUBLIC );
		if (DEBUG == 0) {
			$this->registerActions ( 1 );
		}
		if (DEBUG == 1) {
			$this->registerActions ( 0 );
		}
	}
	public function update($_action = false) {
		$this->updated = false;
		if ($_action)
			$this->action = $_action;
		if ($this->action)
			$action = $this->action;
		else
			$action = $this->checkAction ();
		if (! $action) {
			$this->Vals->URLparams ( $this->sysMod->defQueryString );
			$action = $this->actionDefault;
		}
        $user_id = $this->User->getUserID ();

        $this->User->nView->viewLoginParams ( '', '', $user_id, array (), array () );
        $this->updated = true;

		/********************************************************************************/
		if ($action == 'register'){
            $name = $this->Vals->getVal ( 'name', 'POST', 'string' );
            $phone = $this->Vals->getVal ( 'phone', 'POST', 'string' );
            $pin_code = mt_rand(1000, 9999);
            $sms_id = $this->send_sms($phone,$pin_code);
            if (!$sms_id) {
                echo "<div class='alert alert-danger'>Ошибка отправки СМС.</div>";
            }else {
                $result = $this->nModel->createUser($name, $phone, $pin_code, $sms_id);
                if (!$result) {
                    echo "<div class='alert alert-warning'>Пользователь с таким телефоном уже зарегестрирован.</div>";
                }
                echo "<div class='alert alert-success'>$name, спасибо за регистрацию. Временный пароль $pin_code для входа отправлен на номер: $phone</div>";
            }
            exit();
        }

		if ($action == 'view') {
            $news = $this->nModel->getNewsList(3);
            $prices = $this->nModel->getPrices();
            $add_prices = $this->nModel->getAddPrices();
			$this->nView->view_Index ( $news, $prices, $add_prices );
			$this->updated = true;
		}
		
		/********************************************************************************/
		
	}
	function send_sms($phone, $pin_code){
        $smsru = new SMSRU('69da81b5-ee1e-d004-a1aa-ac83d2687954'); // Ваш уникальный программный ключ, который можно получить на главной странице

        $data = new stdClass();
        $data->to = $phone;
        $data->text = "Для доступа к fl-taxi.ru используйте логин $phone и пароль $pin_code"; // Текст сообщения
// $data->from = ''; // Если у вас уже одобрен буквенный отправитель, его можно указать здесь, в противном случае будет использоваться ваш отправитель по умолчанию
// $data->time = time() + 7*60*60; // Отложить отправку на 7 часов
// $data->translit = 1; // Перевести все русские символы в латиницу (позволяет сэкономить на длине СМС)
// $data->test = 1; // Позволяет выполнить запрос в тестовом режиме без реальной отправки сообщения
// $data->partner_id = '1'; // Можно указать ваш ID партнера, если вы интегрируете код в чужую систему
        $data->test = 1; // Позволяет выполнить запрос в тестовом режиме без реальной отправки сообщения
        $sms = $smsru->send_one($data); // Отправка сообщения и возврат данных в переменную

        if ($sms->status == "OK") { // Запрос выполнен успешно
            echo "<div class='alert alert-success'>Сообщение отправлено успешно.</div>";
//            echo "ID сообщения: $sms->sms_id.";
            return $sms->sms_id;
        } else {
            echo "<div class='alert alert-success'>Сообщение не отправлено. <br/>Код ошибки: $sms->status_code. <br/>Текст ошибки: $sms->status_text.</div>";
            return false;
        }
    }
}
/*************************************/
class titleView extends module_View {
	public function __construct($modName, $sysMod) {
		parent::__construct ( $modName, $sysMod );
		$this->pXSL = array ();
	}
	
	public function view_Index($news, $prices, $add_prices) {
		$Container = $this->newContainer ( 'index' );
		$this->pXSL [] = RIVC_ROOT . 'layout/' . $this->modName . '/index.view.xsl';

        $ContainerNews = $this->addToNode ( $Container, 'news', '' );
        foreach ( $news as $item ) {
            $this->arrToXML ( $item, $ContainerNews, 'item' );
        }
        $ContainerPrices = $this->addToNode ( $Container, 'prices', '' );
        foreach ( $prices as $item ) {
            $this->arrToXML ( $item, $ContainerPrices, 'item' );
        }
        $ContainerAddPrices = $this->addToNode ( $Container, 'add_prices', '' );
        foreach ( $add_prices as $item ) {
            $this->arrToXML ( $item, $ContainerAddPrices, 'item' );
        }
	}

}
/*************************************/
