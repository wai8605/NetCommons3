<?php
/**
 * ディレクトリ定義
 * plugin等をテンプレートではなくコントローラからカスタマイズする場合に用いる
 * ディレクトリを予め準備しておく。
 */
define('CUSTOM_DIR', 'custom');
if (!defined('CUSTOM')) {
	define('CUSTOM', ROOT . DS . CUSTOM_DIR . DS);
}

define('NC_MODINFO_FILENAME',     'modinfo.ini');

//-----------------定義ファイル-------------------------------------------
define('NC_INSTALL_INC_FILE', 'install.inc.php');
define('NC_VERSION_FILE', 'version.php');

//-----------------タイトル区切り文字-------------------------------------------
define('NC_TITLE_SEPARATOR',     ' - ');

//-----------------共通-------------------------------------------
define("_ON",1);
define("_OFF",0);

/**
 * 日付フォーマット
 */
define('NC_DB_DATE_FORMAT', 'Y-m-d H:i:s');
define('NC_DEFAULT_TIME', '12:00am');
define('NC_CHECK_DATETIME', '%^(?:(?:(?:(?:(?:1[6-9]|[2-9]\\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00)))(\\/|-|\\.|\\x20)(?:0?2\\1(?:29)))|(?:(?:(?:1[6-9]|[2-9]\\d)?\\d{2})(\\/|-|\\.|\\x20)(?:(?:(?:0?[13578]|1[02])\\2(?:31))|(?:(?:0?[1,3-9]|1[0-2])\\2(29|30))|(?:(?:0?[1-9])|(?:1[0-2]))\\2(?:0?[1-9]|1\\d|2[0-8])))) ((0?[1-9]|1[012])(:[0-5]\d){0,2} ?([AP]M|[ap]m))$|([01]\d|2[0-3])(:[0-5]\d){0,2}$%');
// 日時チェック用
define('NC_VALIDATOR_DATE_TIME', "YmdHis");

//-----------------長さチェック用-------------------------------------------
define('NC_VALIDATOR_TITLE_LEN', 100);
define('NC_VALIDATOR_MAIL_LEN', 256);
define('NC_VALIDATOR_TEXTAREA_LEN', 60000);

//-----------------禁止URL-------------------------------------------
define('NC_PROHIBITION_URL', '/^(users\/|controls\/)/i');

//-----------------DOCYTPE-------------------------------------------
define('NC_DOCTYPE_STR', '/^[\s\r\n]*<!DOCTYPE html/i');

//-----------------権限(authorities.authority_id)-------------------------------------------
define('NC_AUTH_OTHER_ID', 0);
define('NC_AUTH_GUEST_ID', 5);
define('NC_AUTH_GENERAL_ID', 4);
define('NC_AUTH_MODERATE_ID', 3);
define('NC_AUTH_CHIEF_ID', 2);
define('NC_AUTH_ADMIN_ID', 1);

//-----------------権限(authorities.hierarchy)-------------------------------------------
// 権限が大きくなるほど、高い権限を有する
define('NC_AUTH_OTHER', 0);
define('NC_AUTH_GUEST', 1);
define('NC_AUTH_MIN_GENERAL', 101);
define('NC_AUTH_GENERAL', 200);
define('NC_AUTH_MIN_MODERATE', 201);
define('NC_AUTH_MODERATE', 300);
define('NC_AUTH_MIN_CHIEF', 301);
define('NC_AUTH_CHIEF', 400);
define('NC_AUTH_MIN_ADMIN', 401);
define('NC_AUTH_ADMIN', 500);

define('NC_AUTH_KEY', 'Auth');
define('NC_CONFIG_KEY', 'Config');
define('NC_THEME_KEY', 'Theme');
define('NC_SYSTEM_KEY', 'System');

//-----------------display_flag-------------------------------------------

define('NC_DISPLAY_FLAG_OFF', 0);
define('NC_DISPLAY_FLAG_ON', 1);
define('NC_DISPLAY_FLAG_DISABLE', 2);

//-----------------community allow_flag-------------------------------------------

define('NC_ALLOW_FLAG_OFF', 0);
define('NC_ALLOW_FLAG_ON', 1);
define('NC_ALLOW_FLAG_ONLY_USER', 2);

//-----------------community participate_flag-------------------------------------------

define('NC_PARTICIPATE_FLAG_FREE', 3);				// 参加受付制(希望者は誰でも参加可能）
define('NC_PARTICIPATE_FLAG_ACCEPT', 2);			// 承認制（主担の承認が必要）
define('NC_PARTICIPATE_FLAG_INVITE', 1);			// 招待制（コミュニティーメンバーから招待を受けたユーザのみ参加可能）
define('NC_PARTICIPATE_FLAG_ONLY_USER', 0);			// 参加会員のみ

//-----------------space_type-------------------------------------------

define('NC_SPACE_TYPE_PUBLIC', 1);
define('NC_SPACE_TYPE_MYPORTAL', 2);
define('NC_SPACE_TYPE_PRIVATE', 3);
define('NC_SPACE_TYPE_GROUP', 4);

//-----------------accept_flag-------------------------------------------

define('NC_ACCEPT_FLAG_OFF', 0);
define('NC_ACCEPT_FLAG_ON', 1);
// define('NC_SPACE_TYPE_PENDING', 2);	// TODO:承認機能は未実装

//-----------------room_id-------------------------------------------

define('NC_PUBLIC_ROOM_ID', 9);

//-----------------TOPノード-------------------------------------------
define('NC_TOP_PUBLIC_ID',       1);
define('NC_TOP_MYPORTAL_ID',     2);
define('NC_TOP_PRIVATE_ID',      3);
define('NC_TOP_GROUP_ID',        4);

//-----------------page_id-------------------------------------------

define('NC_HEADER_PAGE_ID', 5);
define('NC_LEFT_PAGE_ID', 6);
define('NC_RIGHT_PAGE_ID', 7);
define('NC_FOOTER_PAGE_ID', 8);

//-----------------permalink-------------------------------------------

define('NC_PERMALINK_CONTENT', '(%| |#|<|>|\+|\\\\|\"|\'|&|\?|\.$|=|\/|~|:|;|,|\$|@|^\.|\||\]|\[|\!|\(|\)|\*)');
define('NC_PERMALINK_PROHIBITION', "/".NC_PERMALINK_CONTENT."/i");
define('NC_PERMALINK_PROHIBITION_REPLACE', "-");
define('NC_PERMALINK_DIR_CONTENT', "^(users\/|controls\/|blocks\/|active-blocks\/|img\/)$");
//define('NC_PERMALINK_DIR_CONTENT', "^(users\/|img\/|theme\/|frame\/|blocks\/|active-blocks\/)$");
define('NC_PERMALINK_PROHIBITION_DIR_PATTERN', "/".NC_PERMALINK_DIR_CONTENT."/i");

define('NC_SPACE_PUBLIC_PREFIX', '');
define('NC_SPACE_MYPORTAL_PREFIX', 'myportal');
define('NC_SPACE_PRIVATE_PREFIX', 'private');
define('NC_SPACE_GROUP_PREFIX', 'community');
//-----------------page_styles(page_infs)-------------------------------------------
define('NC_PAGE_STYLE_PUBLIC_ID',       1);
define('NC_PAGE_STYLE_MYPORTAL_ID',     2);
define('NC_PAGE_STYLE_PRIVATE_ID',      3);
define('NC_PAGE_STYLE_GROUP_ID',        4);
define('NC_PAGE_STYLE_COMMON_ID',       5);

//-----------------page_columns-------------------------------------------

define('NC_PAGE_COLUMN_PUBLIC_ID',       1);
define('NC_PAGE_COLUMN_MYPORTAL_ID',     2);
define('NC_PAGE_COLUMN_PRIVATE_ID',      3);
define('NC_PAGE_COLUMN_GROUP_ID',        4);
//define('NC_PAGE_COLUMN_COMMON_ID',       5);

//-----------------configs-------------------------------------------
define('NC_SYSTEM_CATID',      0);
define('NC_LOGIN_CATID',       1);
define('NC_SERVER_CATID',      2);
define('NC_MAIL_CATID',        3);
define('NC_META_CATID',        4);
define('NC_MEMBERSHIP_CATID',  5);
define('NC_DEVELOPMENT_CATID', 6);
define('NC_SECURITY_CATID',    7);

//-----------------autologin_use-------------------------------------------

define('NC_AUTOLOGIN_OFF', 0);		// 自動ログインOFF
define('NC_AUTOLOGIN_LOGIN', 1);	// ログインIDをクッキーに保持
define('NC_AUTOLOGIN_ON', 2);		// 自動ログイン

//-----------------User.active_flag-------------------------------------------

define('NC_USER_IS_ACTIVE_OFF',     0);		//利用不可
define('NC_USER_IS_ACTIVE_ON',      1);		//利用可能
define('NC_USER_IS_ACTIVE_PENDING', 2);		//承認待ち
define('NC_USER_IS_ACTIVE_MAILED',  3);		//承認済み

//-----------------自動登録-------------
define('NC_AUTOREGIST_SELF', 0);					//ユーザ自身の確認が必要
define('NC_AUTOREGIST_AUTO' ,1);					//自動的にアカウントを有効にする
define('NC_AUTOREGIST_ADMIN', 2);					//管理者の承認が必要

//-----------------ヘッダーメニュー表示-------------
define('NC_HEADER_MENU_NONE', 0);					//ログイン前非表示
define('NC_HEADER_MENU_MOUSEOVER' ,1);				//マウスオーバー時表示
define('NC_HEADER_MENU_CLICK', 2);					//クリック時表示
define('NC_HEADER_MENU_ALWAYS', 3);					//常に表示

//-----------------Item.type-------------------------------------------

define('NC_ITEM_TYPE_TEXT',         "text");
define('NC_ITEM_TYPE_CHECKBOX',     "checkbox");
define('NC_ITEM_TYPE_RADIO',        "radio");
define('NC_ITEM_TYPE_SELECT',       "select");
define('NC_ITEM_TYPE_TEXTAREA',     "textarea");
define('NC_ITEM_TYPE_EMAIL',        "email");
define('NC_ITEM_TYPE_MOBILE_EMAIL', "mobile_email");
define('NC_ITEM_TYPE_LABEL',        "label");
define('NC_ITEM_TYPE_PASSWORD',     "password");
define('NC_ITEM_TYPE_FILE',         "file");

//-----------------システム管理者ID-------------------------------------------
define('NC_SYSTEM_USER_ID',       1);
/**
 * Mode
 */
define('NC_GENERAL_MODE', 0);
define('NC_BLOCK_MODE', 1);

/*
 * アップロード関連
 */
define("NC_ALLOW_ATTACHMENT_NO", 0);
define("NC_ALLOW_ATTACHMENT_IMAGE" ,1);
define("NC_ALLOW_ATTACHMENT_ALL", 2);

// 画像最大アップロードサイズ
define("NC_UPLOAD_MAX_SIZE_IMAGE", 2000000);
define("NC_UPLOAD_MAX_SIZE_ATTACHMENT", 2000000);

define("NC_UPLOAD_MAX_WIDTH_IMAGE", 1024);
define("NC_UPLOAD_MAX_HEIGHT_IMAGE", 1280);

define("NC_UPLOAD_MAX_WIDTH_AVATAR", 145);
define("NC_UPLOAD_MAX_HEIGHT_AVATAR", 145);
define("NC_UPLOAD_MAX_WIDTH_AVATAR_THUMBNAIL", 66);
define("NC_UPLOAD_MAX_HEIGHT_AVATAR_THUMBNAIL", 66);
define("NC_UPLOAD_USER_CONTROLLER_ACTION", 'nccommon/download_avatar');

define('NC_UPLOAD_FOLDER_MODE', 0777);
define('NC_UPLOAD_FILE_MODE', 0666);

//define('NC_UPLOAD_IMAGEFILE_TYPE', 'image/gif,image/jpg,image/jpeg,image/pjpeg,image/pipeg,image/png,image/x-png,image/tiff,image/bmp');

define('NC_UPLOAD_IMAGEFILE_EXTENSION', 'gif,jpg,jpe,jpeg,png,bmp');
define('NC_UPLOAD_ATTACHMENT_EXTENSION', 'Config');		// configテーブルの許す拡張子の一覧から拡張子チェックを行う
define('NC_UPLOAD_COMPRESSIONFILE_EXTENSION', 'zip,tar,tgz,gz');

define("NC_CATEGORY_INIFILE",          "category.ini");
define("NC_THEME_INIFILE",             "theme.ini");