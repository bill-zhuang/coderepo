<?php

class Bill_Constant
{
    const LOGIN_TIMEOUT = 3600; //second

    const INIT_START_PAGE = 1;
    const INIT_PAGE_LENGTH = 25;
    const INIT_TOTAL_PAGE = 1;

    const VALID_STATUS = 1;
    const INVALID_STATUS = 0;

    const INIT_AFFECTED_ROWS = 0;

    const INVALID_PRIMARY_ID = 0;

    const DAY_SECONDS = 86400;

    //http method
    const HTTP_METHOD_GET = 'GET';
    const HTTP_METHOD_POST = 'POST';

    const PRODUCTION_HOST = 'production.host';
    const ALPHA_HOST = 'alpha.host';

    //db config name in application.ini
    const LOCAL_DB = 'localdb';
    const ALPHA_DB = 'alphadb';
    const RELEASE_DB = 'releasedb';

    //sql query cost warning
    const SQL_QUERY_COST_TRIGGER = 10; //second

    //user section
    const ADMIN_NAME = 'admin';
    const DEFAULT_PASSWORD = '123456';
    const DEFAULT_ROLE = 1;
    const SALT_STRING_LENGTH = 64;

    //
    const ACTION_ERROR_INFO = 'Invalid request or parameters.';

    //
    const DEFAULT_WEIGHT = 0;

    //sql like type
    const LIKE_FULL = 0;
    const LIKE_LEFT = 1;
    const LIKE_RIGHT = 2;

    //acl map key
    const ACL_MAP_NAME = 'acl_map';

    //eject type
    const EJECT_TYPE_DREAM = 1;
    const EJECT_TYPE_BAD = 2;
} 