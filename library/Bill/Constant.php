<?php

class Bill_Constant
{
    const INIT_START_PAGE = 1;
    const INIT_PAGE_LENGTH = 25;
    const INIT_TOTAL_PAGE = 1;

    const VALID_STATUS = 1;
    const INVALID_STATUS = 0;

    const INIT_AFFECTED_ROWS = 0;

    const INVALID_PRIMARY_ID = 0;

    const DAY_SECONDS = 86400;

    const PRODUCTION_HOST = 'production.host';
    const ALPHA_HOST = 'alpha.host';

    //user section
    const ADMIN_NAME = 'admin';
    const DEFAULT_PASSWORD = '123456';
    const DEFAULT_ROLE = 1;
    const SALT_STRING_LENGTH = 64;

    //
    const ACTION_ERROR_INFO = 'Invalid request or parameters.';

    //
    const DEFAULT_WEIGHT = 0;
} 