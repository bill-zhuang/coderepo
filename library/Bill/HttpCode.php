<?php
/**
 * Created by bill-zhuang.
 * User: bill-zhuang
 * Date: 15-11-18
 * Time: 下午3:41
 * Reference: https://en.wikipedia.org/wiki/List_of_HTTP_status_codes
 */

class Bill_HttpCode
{
    //1xx Informational
    const Continue1 = 100;
    const Switching_Protocols = 101;
    const Processing = 102;

    //2xx Success
    const OK = 200;
    const Created = 201;
    const Accepted = 202;
    const Non_Authoritative_Information = 203;
    const No_Content = 204;
    const Reset_Content = 205;
    const Partial_Content = 206;
    const Multi_Status = 207;
    const Already_Reported = 208;
    const IM_Used = 226;

    //3xx Redirection
    const Multiple_Choices = 300;
    const Moved_Permanently = 301;
    const Found = 302;
    const See_Other = 303;
    const Not_Modified = 304;
    const Use_Proxy = 305;
    const Switch_Proxy = 306;
    const Temporary_Redirect = 307;
    const Permanent_Redirect = 308;
    const Resume_Incomplete = 308;

    //4xx Client Error
    const error_on_German_Wikipedia = 404;
    const Bad_Request = 400;
    const Unauthorized = 401;
    const Payment_Required = 402;
    const Forbidden = 403;
    const Not_Found = 404;
    const Method_Not_Allowed = 405;
    const Not_Acceptable = 406;
    const Proxy_Authentication_Required = 407;
    const Request_Timeout = 408;
    const Conflict = 409;
    const Gone = 410;
    const Length_Required = 411;
    const Precondition_Failed = 412;
    const Payload_Too_Large = 413;
    const URI_Too_Long = 414;
    const Unsupported_Media_Type = 415;
    const Range_Not_Satisfiable = 416;
    const Expectation_Failed = 417;
    const Im_a_teapot = 418;
    const Authentication_Timeout = 419;
    const Method_Failure = 420;
    const Enhance_Your_Calm = 420;
    const Misdirected_Request = 421;
    const Unprocessable_Entity = 422;
    const Locked = 423;
    const Failed_Dependency = 424;
    const Upgrade_Required = 426;
    const Precondition_Required = 428;
    const Too_Many_Requests = 429;
    const Request_Header_Fields_Too_Large = 431;
    const Login_Timeout = 440;
    const No_Response = 444;
    const Retry_With = 449;
    const Blocked_by_Windows_Parental_Controls = 450;
    const Unavailable_For_Legal_Reasons = 451;
    const Redirect = 451;
    const Request_Header_Too_Large = 494;
    const Cert_Error = 495;
    const No_Cert = 496;
    const HTTP_to_HTTPS = 497;
    const Token_expired_invalid = 498;
    const Client_Closed_Request = 499;
    const Token_required = 499;

    //5xx Server Error
    const Internal_Server_Error = 500;
    const Not_Implemented = 501;
    const Bad_Gateway = 502;
    const Service_Unavailable = 503;
    const Gateway_Timeout = 504;
    const HTTP_Version_Not_Supported = 505;
    const Variant_Also_Negotiates = 506;
    const Insufficient_Storage = 507;
    const Loop_Detected = 508;
    const Bandwidth_Limit_Exceeded = 509;
    const Not_Extended = 510;
    const Network_Authentication_Required = 511;
    const Unknown_Error = 520;
    const Origin_Connection_Time_out = 522;
    const Network_read_timeout_error = 598;
    const Network_connect_timeout_error = 599;
} 