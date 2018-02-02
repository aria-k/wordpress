<?php
/**
 * Базовая конфигурация WordPress.
 *
 * Данный файл содержит конфигурацию следующих параметров: настройки MySQL, префикс таблиц,
 * секретные ключи, язык WordPress и ABSPATH. Вы можете почитать подробнее, зайдя на
 * страницу {@link http://codex.wordpress.org/Editing_wp-config.php Редактирование
 * wp-config.php} кодекса. Вы можете узнать настройки MySQL у Вашего хостера.
 *
 * Данный файл используется при создании wp-config.php во время установки.
 * Однако Вам не обязательно пользоваться Веб-интерфейсом, Вы можете просто скопировать его в
 * "wp-config.php" и самостоятельно заполнить значения.
 *
 * @package WordPress
 */

// ** Настройки MySQL - Вы можете получить эти данные у Вашего хостера ** //
/** Название базы данных WordPress */
define('DB_NAME', 'wordpress_database');

/** Имя пользователя MySQL */
define('DB_USER', 'kmv');

/** Пароль MySQL */
define('DB_PASSWORD', '1992');

/** Хост MySQL */
define('DB_HOST', 'localhost');

/** Кодировка СУБД, используемая при создании таблиц. Едва ли Вам потребуется это изменять. */
define('DB_CHARSET', 'utf8');

/** Способ сравнения строк в СУБД. Не меняйте это значение, если сомневаетесь. */
define('DB_COLLATE', '');

/**#@+
 * Уникальные ключи аутентификации.
 *
 * Поменяйте эти строки на другие уникальные фразы! Если Вы этого не сделаете, безопасность Вашего блога будет под угрозой.
 * Вы можете сгенерировать их при помощи специального сервиса {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * Вы можете поменять их в любой момент. Это приведет к тому, что всем пользователям нужно будет входить в систему заново.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '@b+*!Pya&Xo}PPpCd(]R;@UWdbVB!Bb2guI-?nxHl,i1-xYp$<,U)yq]Sv?<<crV');
define('SECURE_AUTH_KEY',  'Iw?7|ALW?MS0y,+gmvv!_z4oc[vk mrrF:,>rm`n8NzI|VHfZ($k^pE/^R}Imi3>');
define('LOGGED_IN_KEY',    'cYkxWO*zg-Gk9r!CU*TOcP_ao&%yiuy_wa ;4s9OR]p(2>0hJlngj7JGv4}Js_fV');
define('NONCE_KEY',        '3F?Pe8Y+{ozo!<nIp#WgivjR?/n|_e5hFb?fbATpx>V|sP}t@~^w,I$;:F;Kh=z%');
define('AUTH_SALT',        'Jd=:I]qx&zM7GdczeVO!Rkg*KgbeDg|`~uspmSki}V]},&aC{croS`hP@+3Mc.U|');
define('SECURE_AUTH_SALT', 'Tubk_U UEifjx}]^d+_?E}9ER|7d;eq-%w,Uq+#.Dy#r ~JG-eFqp+>`0hzb<b>5');
define('LOGGED_IN_SALT',   '4ZK#%lDYbx|v~=*%_@/Dp>H9Z1O?/lmPD57C*N*%JTn#gM/sfi1ZDLe~e:$oxxoE');
define('NONCE_SALT',       'o4U%hS)MnJ39xalA[MYG@VDsHg$-+t+JVJ-zo}E%rOfsN|02{D{d-p((3^Y[=/s)');

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Вы можете иметь несколько установок в одной БД, давая им различные префиксы.
 * Пожалуйста, используйте только латинские буквы, арабские цифры и знаки подчеркивания!
 */
$table_prefix  = 'wp_';

/**
 * Язык локализации WordPress.
 *
 */
define ('WPLANG', 'ru_RU');

/**
 * Для разработчиков: включение режима отладки WordPress.
 *
 * Поменяйте это значение на true, если хотите видеть сообщения по ходу разработки.
 * Крайне рекомендуется использовать WP_DEBUG разрабочикам тем и плагинов в своей среде разработки.
 */
define('WP_DEBUG', false);

/* Все, больше редактировать ничего не надо! Счастливых публикаций. */

/** Абсолютный путь к каталогу WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Настраивает переменные и модули WordPress. */
require_once(ABSPATH . 'wp-settings.php'); //Видимо здесь кроется причина  отправки на XAMPP(index.php)
