<?php
namespace CpPress\Application\BackEnd;

use \Commonhelp\WP\WPController;
use \Commonhelp\App\Http\RequestInterface;
use CpPress\Application\WP\Admin\Settings;
use CpPress\Application\WP\Admin\PostMeta;
use CpPress\CpPress;

class MultiLanguageController extends WPController{
	
	private $counriesISO3166 = 'a:248:{s:2:"AF";s:11:"AFGHANISTAN";s:2:"AL";s:7:"ALBANIA";s:2:"DZ";s:7:"ALGERIA";s:2:"AS";s:14:"AMERICAN SAMOA";s:2:"AD";s:7:"ANDORRA";s:2:"AO";s:6:"ANGOLA";s:2:"AI";s:8:"ANGUILLA";s:2:"AQ";s:10:"ANTARCTICA";s:2:"AG";s:19:"ANTIGUA AND BARBUDA";s:2:"AR";s:9:"ARGENTINA";s:2:"AM";s:7:"ARMENIA";s:2:"AW";s:5:"ARUBA";s:2:"AU";s:9:"AUSTRALIA";s:2:"AT";s:7:"AUSTRIA";s:2:"AZ";s:10:"AZERBAIJAN";s:2:"BS";s:7:"BAHAMAS";s:2:"BH";s:7:"BAHRAIN";s:2:"BD";s:10:"BANGLADESH";s:2:"BB";s:8:"BARBADOS";s:2:"BY";s:7:"BELARUS";s:2:"BE";s:7:"BELGIUM";s:2:"BZ";s:6:"BELIZE";s:2:"BJ";s:5:"BENIN";s:2:"BM";s:7:"BERMUDA";s:2:"BT";s:6:"BHUTAN";s:2:"BO";s:31:"BOLIVIA, PLURINATIONAL STATE OF";s:2:"BQ";s:32:"BONAIRE, SINT EUSTATIUS AND SABA";s:2:"BA";s:22:"BOSNIA AND HERZEGOVINA";s:2:"BW";s:8:"BOTSWANA";s:2:"BV";s:13:"BOUVET ISLAND";s:2:"BR";s:6:"BRAZIL";s:2:"IO";s:30:"BRITISH INDIAN OCEAN TERRITORY";s:2:"BN";s:17:"BRUNEI DARUSSALAM";s:2:"BG";s:8:"BULGARIA";s:2:"BF";s:12:"BURKINA FASO";s:2:"BI";s:7:"BURUNDI";s:2:"KH";s:8:"CAMBODIA";s:2:"CM";s:8:"CAMEROON";s:2:"CA";s:6:"CANADA";s:2:"CV";s:10:"CAPE VERDE";s:2:"KY";s:14:"CAYMAN ISLANDS";s:2:"CF";s:24:"CENTRAL AFRICAN REPUBLIC";s:2:"TD";s:4:"CHAD";s:2:"CL";s:5:"CHILE";s:2:"CN";s:5:"CHINA";s:2:"CX";s:16:"CHRISTMAS ISLAND";s:2:"CC";s:23:"COCOS (KEELING) ISLANDS";s:2:"CO";s:8:"COLOMBIA";s:2:"KM";s:7:"COMOROS";s:2:"CG";s:5:"CONGO";s:2:"CD";s:37:"CONGO, THE DEMOCRATIC REPUBLIC OF THE";s:2:"CK";s:12:"COOK ISLANDS";s:2:"CR";s:10:"COSTA RICA";s:2:"CI";s:12:"COTE DIVOIRE";s:2:"HR";s:7:"CROATIA";s:2:"CU";s:4:"CUBA";s:2:"CW";s:7:"CURACAO";s:2:"CY";s:6:"CYPRUS";s:2:"CZ";s:14:"CZECH REPUBLIC";s:2:"DK";s:7:"DENMARK";s:2:"DJ";s:8:"DJIBOUTI";s:2:"DM";s:8:"DOMINICA";s:2:"DO";s:18:"DOMINICAN REPUBLIC";s:2:"EC";s:7:"ECUADOR";s:2:"EG";s:5:"EGYPT";s:2:"SV";s:11:"EL SALVADOR";s:2:"GQ";s:17:"EQUATORIAL GUINEA";s:2:"ER";s:7:"ERITREA";s:2:"EE";s:7:"ESTONIA";s:2:"ET";s:8:"ETHIOPIA";s:2:"FK";s:27:"FALKLAND ISLANDS (MALVINAS)";s:2:"FO";s:13:"FAROE ISLANDS";s:2:"FJ";s:4:"FIJI";s:2:"FI";s:7:"FINLAND";s:2:"FR";s:6:"FRANCE";s:2:"GF";s:13:"FRENCH GUIANA";s:2:"PF";s:16:"FRENCH POLYNESIA";s:2:"TF";s:27:"FRENCH SOUTHERN TERRITORIES";s:2:"GA";s:5:"GABON";s:2:"GM";s:6:"GAMBIA";s:2:"GE";s:7:"GEORGIA";s:2:"DE";s:7:"GERMANY";s:2:"GH";s:5:"GHANA";s:2:"GI";s:9:"GIBRALTAR";s:2:"GR";s:6:"GREECE";s:2:"GL";s:9:"GREENLAND";s:2:"GD";s:7:"GRENADA";s:2:"GP";s:10:"GUADELOUPE";s:2:"GU";s:4:"GUAM";s:2:"GT";s:9:"GUATEMALA";s:2:"GG";s:8:"GUERNSEY";s:2:"GN";s:6:"GUINEA";s:2:"GW";s:13:"GUINEA-BISSAU";s:2:"GY";s:6:"GUYANA";s:2:"HT";s:5:"HAITI";s:2:"HM";s:33:"HEARD ISLAND AND MCDONALD ISLANDS";s:2:"VA";s:29:"HOLY SEE (VATICAN CITY STATE)";s:2:"HN";s:8:"HONDURAS";s:2:"HK";s:9:"HONG KONG";s:2:"HU";s:7:"HUNGARY";s:2:"IS";s:7:"ICELAND";s:2:"IN";s:5:"INDIA";s:2:"ID";s:9:"INDONESIA";s:2:"IR";s:25:"IRAN, ISLAMIC REPUBLIC OF";s:2:"IQ";s:4:"IRAQ";s:2:"IE";s:7:"IRELAND";s:2:"IM";s:11:"ISLE OF MAN";s:2:"IL";s:6:"ISRAEL";s:2:"IT";s:5:"ITALY";s:2:"JM";s:7:"JAMAICA";s:2:"JP";s:5:"JAPAN";s:2:"JE";s:6:"JERSEY";s:2:"JO";s:6:"JORDAN";s:2:"KZ";s:10:"KAZAKHSTAN";s:2:"KE";s:5:"KENYA";s:2:"KI";s:8:"KIRIBATI";s:2:"KP";s:37:"KOREA, DEMOCRATIC PEOPLES REPUBLIC OF";s:2:"KR";s:18:"KOREA, REPUBLIC OF";s:2:"KW";s:6:"KUWAIT";s:2:"KG";s:10:"KYRGYZSTAN";s:2:"LA";s:31:"LAO PEOPLES DEMOCRATIC REPUBLIC";s:2:"LV";s:6:"LATVIA";s:2:"LB";s:7:"LEBANON";s:2:"LS";s:7:"LESOTHO";s:2:"LR";s:7:"LIBERIA";s:2:"LY";s:5:"LIBYA";s:2:"LI";s:13:"LIECHTENSTEIN";s:2:"LT";s:9:"LITHUANIA";s:2:"LU";s:10:"LUXEMBOURG";s:2:"MO";s:5:"MACAO";s:2:"MK";s:42:"MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF";s:2:"MG";s:10:"MADAGASCAR";s:2:"MW";s:6:"MALAWI";s:2:"MY";s:8:"MALAYSIA";s:2:"MV";s:8:"MALDIVES";s:2:"ML";s:4:"MALI";s:2:"MT";s:5:"MALTA";s:2:"MH";s:16:"MARSHALL ISLANDS";s:2:"MQ";s:10:"MARTINIQUE";s:2:"MR";s:10:"MAURITANIA";s:2:"MU";s:9:"MAURITIUS";s:2:"YT";s:7:"MAYOTTE";s:2:"MX";s:6:"MEXICO";s:2:"FM";s:31:"MICRONESIA, FEDERATED STATES OF";s:2:"MD";s:20:"MOLDOVA, REPUBLIC OF";s:2:"MC";s:6:"MONACO";s:2:"MN";s:8:"MONGOLIA";s:2:"ME";s:10:"MONTENEGRO";s:2:"MS";s:10:"MONTSERRAT";s:2:"MA";s:7:"MOROCCO";s:2:"MZ";s:10:"MOZAMBIQUE";s:2:"MM";s:7:"MYANMAR";s:2:"NA";s:7:"NAMIBIA";s:2:"NR";s:5:"NAURU";s:2:"NP";s:5:"NEPAL";s:2:"NL";s:11:"NETHERLANDS";s:2:"NC";s:13:"NEW CALEDONIA";s:2:"NZ";s:11:"NEW ZEALAND";s:2:"NI";s:9:"NICARAGUA";s:2:"NE";s:5:"NIGER";s:2:"NG";s:7:"NIGERIA";s:2:"NU";s:4:"NIUE";s:2:"NF";s:14:"NORFOLK ISLAND";s:2:"MP";s:24:"NORTHERN MARIANA ISLANDS";s:2:"NO";s:6:"NORWAY";s:2:"OM";s:4:"OMAN";s:2:"PK";s:8:"PAKISTAN";s:2:"PW";s:5:"PALAU";s:2:"PS";s:19:"PALESTINE, STATE OF";s:2:"PA";s:6:"PANAMA";s:2:"PG";s:16:"PAPUA NEW GUINEA";s:2:"PY";s:8:"PARAGUAY";s:2:"PE";s:4:"PERU";s:2:"PH";s:11:"PHILIPPINES";s:2:"PN";s:8:"PITCAIRN";s:2:"PL";s:6:"POLAND";s:2:"PT";s:8:"PORTUGAL";s:2:"PR";s:11:"PUERTO RICO";s:2:"QA";s:5:"QATAR";s:2:"RE";s:7:"REUNION";s:2:"RO";s:7:"ROMANIA";s:2:"RU";s:18:"RUSSIAN FEDERATION";s:2:"RW";s:6:"RWANDA";s:2:"BL";s:20:"SAINT BARTH√âLEMY";s:2:"SH";s:44:"SAINT HELENA, ASCENSION AND TRISTAN DA CUNHA";s:2:"KN";s:21:"SAINT KITTS AND NEVIS";s:2:"LC";s:11:"SAINT LUCIA";s:2:"MF";s:26:"SAINT MARTIN (FRENCH PART)";s:2:"PM";s:25:"SAINT PIERRE AND MIQUELON";s:2:"VC";s:32:"SAINT VINCENT AND THE GRENADINES";s:2:"WS";s:5:"SAMOA";s:2:"SM";s:10:"SAN MARINO";s:2:"ST";s:21:"SAO TOME AND PRINCIPE";s:2:"SA";s:12:"SAUDI ARABIA";s:2:"SN";s:7:"SENEGAL";s:2:"RS";s:6:"SERBIA";s:2:"SC";s:10:"SEYCHELLES";s:2:"SL";s:12:"SIERRA LEONE";s:2:"SG";s:9:"SINGAPORE";s:2:"SX";s:25:"SINT MAARTEN (DUTCH PART)";s:2:"SK";s:8:"SLOVAKIA";s:2:"SI";s:8:"SLOVENIA";s:2:"SB";s:15:"SOLOMON ISLANDS";s:2:"SO";s:7:"SOMALIA";s:2:"ZA";s:12:"SOUTH AFRICA";s:2:"GS";s:44:"SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS";s:2:"SS";s:11:"SOUTH SUDAN";s:2:"ES";s:5:"SPAIN";s:2:"LK";s:9:"SRI LANKA";s:2:"SD";s:5:"SUDAN";s:2:"SR";s:8:"SURINAME";s:2:"SJ";s:22:"SVALBARD AND JAN MAYEN";s:2:"SZ";s:9:"SWAZILAND";s:2:"SE";s:6:"SWEDEN";s:2:"CH";s:11:"SWITZERLAND";s:2:"SY";s:20:"SYRIAN ARAB REPUBLIC";s:2:"TW";s:25:"TAIWAN, PROVINCE OF CHINA";s:2:"TJ";s:10:"TAJIKISTAN";s:2:"TZ";s:28:"TANZANIA, UNITED REPUBLIC OF";s:2:"TH";s:8:"THAILAND";s:2:"TL";s:11:"TIMOR-LESTE";s:2:"TG";s:4:"TOGO";s:2:"TK";s:7:"TOKELAU";s:2:"TO";s:5:"TONGA";s:2:"TT";s:19:"TRINIDAD AND TOBAGO";s:2:"TN";s:7:"TUNISIA";s:2:"TR";s:6:"TURKEY";s:2:"TM";s:12:"TURKMENISTAN";s:2:"TC";s:24:"TURKS AND CAICOS ISLANDS";s:2:"TV";s:6:"TUVALU";s:2:"UG";s:6:"UGANDA";s:2:"UA";s:7:"UKRAINE";s:2:"AE";s:20:"UNITED ARAB EMIRATES";s:2:"GB";s:14:"UNITED KINGDOM";s:2:"US";s:13:"UNITED STATES";s:2:"UM";s:36:"UNITED STATES MINOR OUTLYING ISLANDS";s:2:"UY";s:7:"URUGUAY";s:2:"UZ";s:10:"UZBEKISTAN";s:2:"VU";s:7:"VANUATU";s:2:"VE";s:33:"VENEZUELA, BOLIVARIAN REPUBLIC OF";s:2:"VN";s:8:"VIET NAM";s:2:"VG";s:23:"VIRGIN ISLANDS, BRITISH";s:2:"VI";s:20:"VIRGIN ISLANDS, U.S.";s:2:"WF";s:17:"WALLIS AND FUTUNA";s:2:"EH";s:14:"WESTERN SAHARA";s:2:"YE";s:5:"YEMEN";s:2:"ZM";s:6:"ZAMBIA";s:2:"ZW";s:8:"ZIMBABWE";}';
	
	
	public function __construct($appName, RequestInterface $request, $templateDirs = array()){
		parent::__construct($appName, $request, $templateDirs);
		
	}
	
	public function language($post){
		$country = PostMeta::find($post->ID, 'cp-press-country');
		$countries = unserialize($this->counriesISO3166);
		$flagDir = dirname(dirname(plugin_dir_path(CpPress::$FILE))) . '/assets/flags/1x1';
		$flags = glob($flagDir . '/*.svg');
		$this->assign('selectedCountry', $country);
		$this->assign('countries', $countries);
		$this->assign('flags', $flags);
	}
	
	public function save($id){
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
			return;
		}
	
		if(is_null($this->getParam('_cppress_multilanguage_nonce')) || !wp_verify_nonce($this->getParam('_cppress_multilanguage_nonce'), 'save')){
			return;
		}
		
		if($this->getParam('cp-press-country', null) !== null){
			$files = $this->getParam('cp-press-country');
			update_post_meta($id, 'cp-press-country', $files);
		}
	}
	
}