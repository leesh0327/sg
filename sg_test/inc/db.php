<?php
// 보안설정이나 프레임이 달라도 쿠키가 통하도록 설정
header('P3P: CP="ALL CURa ADMa DEVa TAIa OUR BUS IND PHY ONL UNI PUR FIN COM NAV INT DEM CNT STA POL HEA PRE LOC OTC"');

$ext_arr = array ('PHP_SELF', '_ENV', '_GET', '_POST', '_FILES', '_SERVER', '_COOKIE', '_SESSION', '_REQUEST',
                  'HTTP_ENV_VARS', 'HTTP_GET_VARS', 'HTTP_POST_VARS', 'HTTP_POST_FILES', 'HTTP_SERVER_VARS',
                  'HTTP_COOKIE_VARS', 'HTTP_SESSION_VARS', 'GLOBALS');
$ext_cnt = count($ext_arr);
for ($i=0; $i<$ext_cnt; $i++) {
    // POST, GET 으로 선언된 전역변수가 있다면 unset() 시킴
    if (isset($_GET[$ext_arr[$i]]))  unset($_GET[$ext_arr[$i]]);
    if (isset($_POST[$ext_arr[$i]])) unset($_POST[$ext_arr[$i]]);
}


/********************
    DB 접속
********************/
define('TARA_MYSQL_HOST', 'localhost');
define('TARA_MYSQL_USER', 'tarasoft153624');
define('TARA_MYSQL_PASSWORD', 'tara153624@/');
define('TARA_MYSQL_DB', 'tarasoft153624');

/********************
    시간 상수
********************/
// 서버의 시간과 실제 사용하는 시간이 틀린 경우 수정하세요.
// 하루는 86400 초입니다. 1시간은 3600초
// 6시간이 빠른 경우 time() + (3600 * 6);
// 6시간이 느린 경우 time() - (3600 * 6);
define('TARA_SERVER_TIME',    time());
define('TARA_TIME_YMDHIS',    date('Y-m-d H:i:s', TARA_SERVER_TIME));
define('TARA_TIME_YMD',       substr(TARA_TIME_YMDHIS, 0, 10));
define('TARA_TIME_HIS',       substr(TARA_TIME_YMDHIS, 11, 8));

// 입력값 검사 상수 (숫자를 변경하시면 안됩니다.)
define('TARA_ALPHAUPPER',      1); // 영대문자
define('TARA_ALPHALOWER',      2); // 영소문자
define('TARA_ALPHABETIC',      4); // 영대,소문자
define('TARA_NUMERIC',         8); // 숫자
define('TARA_HANGUL',         16); // 한글
define('TARA_SPACE',          32); // 공백
define('TARA_SPECIAL',        64); // 특수문자

// 퍼미션
define('TARA_DIR_PERMISSION',  0755); // 디렉토리 생성시 퍼미션
define('TARA_FILE_PERMISSION', 0644); // 파일 생성시 퍼미션

// 모바일 인지 결정 $_SERVER['HTTP_USER_AGENT']
define('TARA_MOBILE_AGENT',   'phone|samsung|lgtel|mobile|[^A]skt|nokia|blackberry|android|sony');

// SMTP
// lib/mailer.lib.php 에서 사용
define('TARA_SMTP',      '127.0.0.1');
define('TARA_SMTP_PORT', '25');

// 전산
define('TARA_SYSTEM', '0'); //0은 OFF, 1은 ON



// $member 에 값을 직접 넘길 수 있음
$config = array();
$member = array();
$board  = array();
$group  = array();
$tara   = array();
// customer business name

define('TARA_CUST_BIZ', 'Success Global');


// 테이블
define('TARA_TABLE_PREFIX', 'tc_');

$tara['member_table'] = TARA_TABLE_PREFIX.'customer'; // 회원 테이블
$tara['point_table'] = TARA_TABLE_PREFIX.'point'; // 포인트 테이블
$tara['news_tb'] = TARA_TABLE_PREFIX.'news'; // 포인트 테이블
$tara['prod_tb'] = TARA_TABLE_PREFIX.'prod'; // 쇼핑몰 상품 테이블


// 접속
$conn = sql_connect(TARA_MYSQL_HOST, TARA_MYSQL_USER, TARA_MYSQL_PASSWORD, TARA_MYSQL_DB) or die('MySQL Connect Error!!!');

$tara['connect_db'] = $conn;

@session_cache_limiter('private, must-revalidate');
@session_start();


// QUERY_STRING
$qstr = '';

if (isset($_REQUEST['sca']))  {
    $sca = clean_xss_tags(trim($_REQUEST['sca']));
    if ($sca) {
        $sca = preg_replace("/[\<\>\'\"\\\'\\\"\%\=\(\)\/\^\*]/", "", $sca);
        $qstr .= '&amp;sca=' . urlencode($sca);
    }
} else {
    $sca = '';
}

if (isset($_REQUEST['sfl']))  {
    $sfl = trim($_REQUEST['sfl']);
    $sfl = preg_replace("/[\<\>\'\"\\\'\\\"\%\=\(\)\/\^\*\s]/", "", $sfl);
    if ($sfl)
        $qstr .= '&amp;sfl=' . urlencode($sfl); // search field (검색 필드)
} else {
    $sfl = '';
}

if (isset($_REQUEST['stx']))  { // search text (검색어)
    $stx = get_search_string(trim($_REQUEST['stx']));
    if ($stx)
        $qstr .= '&amp;stx=' . urlencode(cut_str($stx, 20, ''));
} else {
    $stx = '';
}

if (isset($_REQUEST['sst']))  {
    $sst = trim($_REQUEST['sst']);
    $sst = preg_replace("/[\<\>\'\"\\\'\\\"\%\=\(\)\/\^\*\s]/", "", $sst);
    if ($sst)
        $qstr .= '&amp;sst=' . urlencode($sst); // search sort (검색 정렬 필드)
} else {
    $sst = '';
}

if (isset($_REQUEST['sod']))  { // search order (검색 오름, 내림차순)
    $sod = preg_match("/^(asc|desc)$/i", $sod) ? $sod : '';
    if ($sod)
        $qstr .= '&amp;sod=' . urlencode($sod);
} else {
    $sod = '';
}

if (isset($_REQUEST['sop']))  { // search operator (검색 or, and 오퍼레이터)
    $sop = preg_match("/^(or|and)$/i", $sop) ? $sop : '';
    if ($sop)
        $qstr .= '&amp;sop=' . urlencode($sop);
} else {
    $sop = '';
}

if (isset($_REQUEST['spt']))  { // search part (검색 파트[구간])
    $spt = (int)$spt;
    if ($spt)
        $qstr .= '&amp;spt=' . urlencode($spt);
} else {
    $spt = '';
}

if (isset($_REQUEST['page'])) { // 리스트 페이지
    $page = (int)$_REQUEST['page'];
    if ($page)
        $qstr .= '&amp;page=' . urlencode($page);
} else {
    $page = '';
}

if (isset($_REQUEST['w'])) {
    $w = substr($w, 0, 2);
} else {
    $w = '';
}

if (isset($_REQUEST['wr_id'])) {
    $wr_id = (int)$_REQUEST['wr_id'];
} else {
    $wr_id = 0;
}

if (isset($_REQUEST['bo_table'])) {
    $bo_table = preg_replace('/[^a-z0-9_]/i', '', trim($_REQUEST['bo_table']));
    $bo_table = substr($bo_table, 0, 20);
} else {
    $bo_table = '';
}

// URL ENCODING
if (isset($_REQUEST['url'])) {
    $url = strip_tags(trim($_REQUEST['url']));
    $urlencode = urlencode($url);
} else {
    $url = '';
    $urlencode = urlencode($_SERVER['REQUEST_URI']);
}

if (isset($_REQUEST['gr_id'])) {
    if (!is_array($_REQUEST['gr_id'])) {
        $gr_id = preg_replace('/[^a-z0-9_]/i', '', trim($_REQUEST['gr_id']));
    }
} else {
    $gr_id = '';
}


if ($_SESSION['user-id']) { // 로그인중이라면
    $member = get_member($_SESSION['user-id']);

}



/*************************************************************************
**
**  일반 관련 함수 모음
**
*************************************************************************/

// 회원 정보를 얻는다.
function get_member($mb_id, $fields='*')
{
    global $tara;

    return sql_fetch(" select $fields from {$tara['member_table']} where uid = TRIM('$mb_id') ");
}


// 회원 정보를 얻는다.
function get_member_no($mb_no, $fields='*')
{
    global $tara;

    return sql_fetch(" select $fields from {$tara['member_table']} where idx = TRIM('$mb_no') ");
}



// 포인트 정보를 얻는다.
function get_point($mb_id, $fields='*')
{
    global $tara;

    return sql_fetch(" select $fields from {$tara['point_table']} where mb_id = TRIM('$mb_id') and (po_content = 'daily' or po_content = 'level' or po_content = 'position') order by po_id desc limit 1 ");
}


// 포인트 정보를 얻는다.
function get_position_point($mb_id, $fields='*')
{
    global $tara;

    return sql_fetch(" select $fields from {$tara['point_table']} where mb_id = TRIM('$mb_id') and po_content = 'position' order by po_id desc limit 1 ");
}


// 포인트 부여
function insert_point($mb_id, $point, $content='', $rel_table='', $rel_id='', $rel_action='', $expire=0)
{
    global $config;
    global $tara;
    global $is_admin;

    // 포인트가 없다면 업데이트 할 필요 없음
    if ($point == 0) { return 0; }

    // 회원아이디가 없다면 업데이트 할 필요 없음
    if ($mb_id == '') { return 0; }
    $mb = sql_fetch(" select uid from {$tara['member_table']} where uid = '$mb_id' ");
    if (!$mb['uid']) { return 0; }

    // 회원포인트
    $mb_point = get_point_sum($mb_id);

	$daily_point = get_daily_sum($mb_id);
	$level_point = get_level_sum($mb_id);
	$position_point = get_position_sum($mb_id);

    // 이미 등록된 내역이라면 건너뜀
    if ($rel_table || $rel_id || $rel_action || $po_content)
    {
        $sql = " select count(*) as cnt from {$tara['point_table']}
                  where mb_id = '$mb_id'
                    and po_rel_table = '$rel_table'
                    and po_rel_id = '$rel_id'
                    and po_rel_action = '$rel_action'
					and po_content = '$po_content' ";
        $row = sql_fetch($sql);
        if ($row['cnt'])
            return -1;
    }

    // 포인트 건별 생성
    $po_expire_date = '9999-12-31';

    $po_expired = 0;
    if($point < 0) {
        $po_expired = 1;
        $po_expire_date = TARA_TIME_YMD;
    }
    $po_mb_point = $mb_point + $point;

	if ($content == "daily") {
		$po_daily_point = $daily_point + $point;
		$po_level_point = $level_point;
		$po_position_point = $position_point;
	}
	if ($content == "level"){
		$po_daily_point = $daily_point;
		$po_level_point = $level_point + $point;
		$po_position_point = $position_point;
	}
	if ($content == "position") {
		$po_daily_point = $daily_point;
		$po_level_point = $level_point;
		$po_position_point = $position_point + $point;
	}


    $sql = " insert into {$tara['point_table']}
                set mb_id = '$mb_id',
                    po_datetime = '".TARA_TIME_YMDHIS."',
                    po_content = '".addslashes($content)."',
                    po_point = '$point',
                    po_use_point = '0',
                    po_mb_point = '$po_mb_point',
					po_daily_point = '$po_daily_point',
					po_level_point = '$po_level_point',
					po_position_point = '$po_position_point',
                    po_expired = '$po_expired',
                    po_expire_date = '$po_expire_date',
                    po_rel_table = '$rel_table',
                    po_rel_id = '$rel_id',
                    po_rel_action = '$rel_action' ";
    sql_query($sql);

    // 포인트를 사용한 경우 포인트 내역에 사용금액 기록
    if($point < 0) {
        insert_use_point($mb_id, $point);
    }

    // 포인트 UPDATE
    $sql = " update {$tara['member_table']} set upoint = '$po_mb_point' where uid = '$mb_id' ";
    sql_query($sql);

    return 1;
}


// 사용포인트 입력
function insert_use_point($mb_id, $point, $po_id='')
{
    global $tara, $config;

    $sql_order = " order by po_id asc ";

    $point1 = abs($point);
    $sql = " select po_id, po_point, po_use_point
                from {$tara['point_table']}
                where mb_id = '$mb_id'
                  and po_id <> '$po_id'
                  and po_expired = '0'
                  and po_point > po_use_point
                $sql_order ";
    $result = sql_query($sql);
    for($i=0; $row=sql_fetch_array($result); $i++) {
        $point2 = $row['po_point'];
        $point3 = $row['po_use_point'];

        if(($point2 - $point3) > $point1) {
            $sql = " update {$tara['point_table']}
                        set po_use_point = po_use_point + '$point1'
                        where po_id = '{$row['po_id']}' ";
            sql_query($sql);
            break;
        } else {
            $point4 = $point2 - $point3;
            $sql = " update {$tara['point_table']}
                        set po_use_point = po_use_point + '$point4',
                            po_expired = '100'
                        where po_id = '{$row['po_id']}' ";
            sql_query($sql);
            $point1 -= $point4;
        }
    }
}


// 포인트 내역 합계
function get_point_sum($mb_id)
{
    global $tara, $config;

    // 포인트합
    $sql = " select sum(po_point) as sum_po_point
                from {$tara['point_table']}
                where mb_id = '$mb_id' ";
    $row = sql_fetch($sql);

    return $row['sum_po_point'];
}


function get_daily_sum($mb_id)
{
    global $tara, $config;

    // 포인트합
    $sql = " select sum(po_point) as sum_po_point
                from {$tara['point_table']}
                where mb_id = '$mb_id' and po_content = 'daily' ";
    $row = sql_fetch($sql);

    return $row['sum_po_point'];
}


function get_level_sum($mb_id)
{
    global $tara, $config;

    // 포인트합
    $sql = " select sum(po_point) as sum_po_point
                from {$tara['point_table']}
                where mb_id = '$mb_id' and po_content = 'level' ";
    $row = sql_fetch($sql);

    return $row['sum_po_point'];
}

function get_position_sum($mb_id)
{
    global $tara, $config;

    // 포인트합
    $sql = " select sum(po_point) as sum_po_point
                from {$tara['point_table']}
                where mb_id = '$mb_id' and po_content = 'position' ";
    $row = sql_fetch($sql);

    return $row['sum_po_point'];
}


function get_daily_count($mb_id)
{
	global $tara;

	$sql = " select count(*) as cnt
                from {$tara['point_table']}
                where mb_id = '$mb_id' and po_content = 'daily' ";
	$row = sql_fetch($sql);

	return $row['cnt'];
}


function get_level_count($mb_id)
{
	global $tara;

	$sql = " select count(*) as cnt
                from {$tara['point_table']}
                where mb_id = '$mb_id' and po_content = 'level' ";
	$row = sql_fetch($sql);

	return $row['cnt'];
}


function get_position_count($mb_id)
{
	global $tara;

	$sql = " select count(*) as cnt
                from {$tara['point_table']}
                where mb_id = '$mb_id' and po_content = 'position' ";
	$row = sql_fetch($sql);

	return $row['cnt'];
}


function get_level_rec($mb_no)
{
	global $tara;

	$sql = " select count(*) as cnt
                from {$tara['member_table']}
                where urecommender = '$mb_no' ";
	$row = sql_fetch($sql);

	return $row['cnt'];
}


function get_level_num($mb_no)
{
	global $tara;

    $sql_order = " order by idx asc ";

    $sql = " select idx
                from {$tara['member_table']}
                where urecommender = '$mb_no'
                $sql_order ";
    $result = sql_query($sql);

	$leg1 = array();
	$leg2 = array();
	$leg3 = array();
	$leg4 = array();
	$leg5 = array();
	$leg6 = array();
	$leg7 = array();
	$leg8 = array();
	$leg9 = array();
	$leg10 = array();

	$leg11 = array();
	$leg12 = array();
	$leg13 = array();
	$leg14 = array();
	$leg15 = array();
	$leg16 = array();
	$leg17 = array();
	$leg18 = array();
	$leg19 = array();
	$leg20 = array();

	$leg21 = array();
	$leg22 = array();
	$leg23 = array();
	$leg24 = array();
	$leg25 = array();
	$leg26 = array();
	$leg27 = array();
	$leg28 = array();
	$leg29 = array();
	$leg30 = array();

	$leg31 = array();

	$leg_cnt = 0;

		for($j=0; $row=sql_fetch_array($result); $j++) {
			$leg1[] = $row['idx'];
		}

		for($i=0; $i<count($leg1); $i++) {
			$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg1[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg2[] = $row['idx'];
			}
		}

		for($i=0; $i<count($leg2); $i++) {
			$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg2[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg3[] = $row['idx'];
			}
		}

		for($i=0; $i<count($leg3); $i++) {
			$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg3[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg4[] = $row['idx'];
			}
		}

		for($i=0; $i<count($leg4); $i++) {
			$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg4[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg5[] = $row['idx'];
			}
		}

		for($i=0; $i<count($leg5); $i++) {
			$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg5[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg6[] = $row['idx'];
			}
		}

		for($i=0; $i<count($leg6); $i++) {
			$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg6[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg7[] = $row['idx'];
			}
		}

		for($i=0; $i<count($leg7); $i++) {
			$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg7[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg8[] = $row['idx'];
			}
		}

		for($i=0; $i<count($leg8); $i++) {
			$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg8[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg9[] = $row['idx'];
			}
		}

		for($i=0; $i<count($leg9); $i++) {
			$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg9[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg10[] = $row['idx'];
			}
		}

		for($i=0; $i<count($leg10); $i++) {
			$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg10[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg11[] = $row['idx'];
			}
		}

		for($i=0; $i<count($leg11); $i++) {
			$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg11[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg12[] = $row['idx'];
			}
		}

		for($i=0; $i<count($leg12); $i++) {
			$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg12[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg13[] = $row['idx'];
			}
		}

		for($i=0; $i<count($leg13); $i++) {
			$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg13[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg14[] = $row['idx'];
			}
		}

		for($i=0; $i<count($leg14); $i++) {
			$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg14[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg15[] = $row['idx'];
			}
		}

		for($i=0; $i<count($leg15); $i++) {
			$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg15[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg16[] = $row['idx'];
			}
		}

		for($i=0; $i<count($leg16); $i++) {
			$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg16[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg17[] = $row['idx'];
			}
		}

		for($i=0; $i<count($leg17); $i++) {
			$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg17[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg18[] = $row['idx'];
			}
		}

		for($i=0; $i<count($leg18); $i++) {
			$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg18[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg19[] = $row['idx'];
			}
		}

		for($i=0; $i<count($leg19); $i++) {
			$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg19[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg20[] = $row['idx'];
			}
		}

		for($i=0; $i<count($leg20); $i++) {
			$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg20[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg21[] = $row['idx'];
			}
		}

		for($i=0; $i<count($leg21); $i++) {
			$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg21[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg22[] = $row['idx'];
			}
		}

		for($i=0; $i<count($leg22); $i++) {
			$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg22[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg23[] = $row['idx'];
			}
		}

		for($i=0; $i<count($leg23); $i++) {
			$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg23[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg24[] = $row['idx'];
			}
		}

		for($i=0; $i<count($leg24); $i++) {
			$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg24[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg25[] = $row['idx'];
			}
		}

		for($i=0; $i<count($leg25); $i++) {
			$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg25[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg26[] = $row['idx'];
			}
		}

		for($i=0; $i<count($leg26); $i++) {
			$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg26[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg27[] = $row['idx'];
			}
		}

		for($i=0; $i<count($leg27); $i++) {
			$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg27[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg28[] = $row['idx'];
			}
		}

		for($i=0; $i<count($leg28); $i++) {
			$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg28[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg29[] = $row['idx'];
			}
		}

		for($i=0; $i<count($leg29); $i++) {
			$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg29[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg30[] = $row['idx'];
			}
		}

		for($i=0; $i<count($leg30); $i++) {
			$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg30[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg31[] = $row['idx'];
			}
		}

	if (count($leg1) > 0) $leg_cnt = $leg_cnt + 1;
	if (count($leg2) > 0) $leg_cnt = $leg_cnt + 1;
	if (count($leg3) > 0) $leg_cnt = $leg_cnt + 1;
	if (count($leg4) > 0) $leg_cnt = $leg_cnt + 1;
	if (count($leg5) > 0) $leg_cnt = $leg_cnt + 1;
	if (count($leg6) > 0) $leg_cnt = $leg_cnt + 1;
	if (count($leg7) > 0) $leg_cnt = $leg_cnt + 1;
	if (count($leg8) > 0) $leg_cnt = $leg_cnt + 1;
	if (count($leg9) > 0) $leg_cnt = $leg_cnt + 1;
	if (count($leg10) > 0) $leg_cnt = $leg_cnt + 1;

	if (count($leg11) > 0) $leg_cnt = $leg_cnt + 1;
	if (count($leg12) > 0) $leg_cnt = $leg_cnt + 1;
	if (count($leg13) > 0) $leg_cnt = $leg_cnt + 1;
	if (count($leg14) > 0) $leg_cnt = $leg_cnt + 1;
	if (count($leg15) > 0) $leg_cnt = $leg_cnt + 1;
	if (count($leg16) > 0) $leg_cnt = $leg_cnt + 1;
	if (count($leg17) > 0) $leg_cnt = $leg_cnt + 1;
	if (count($leg18) > 0) $leg_cnt = $leg_cnt + 1;
	if (count($leg19) > 0) $leg_cnt = $leg_cnt + 1;
	if (count($leg20) > 0) $leg_cnt = $leg_cnt + 1;

	if (count($leg21) > 0) $leg_cnt = $leg_cnt + 1;
	if (count($leg22) > 0) $leg_cnt = $leg_cnt + 1;
	if (count($leg23) > 0) $leg_cnt = $leg_cnt + 1;
	if (count($leg24) > 0) $leg_cnt = $leg_cnt + 1;
	if (count($leg25) > 0) $leg_cnt = $leg_cnt + 1;
	if (count($leg26) > 0) $leg_cnt = $leg_cnt + 1;
	if (count($leg27) > 0) $leg_cnt = $leg_cnt + 1;
	if (count($leg28) > 0) $leg_cnt = $leg_cnt + 1;
	if (count($leg29) > 0) $leg_cnt = $leg_cnt + 1;
	if (count($leg30) > 0) $leg_cnt = $leg_cnt + 1;

	if (count($leg31) > 0) $leg_cnt = $leg_cnt + 1;

	return $leg_cnt;
}


function get_sales_num($mb_no)
{
	global $tara;

    $sql_order = " order by idx asc ";

    $sql = " select idx, upackage, ucertify
                from {$tara['member_table']}
                where urecommender = '$mb_no'
                $sql_order ";
    $result = sql_query($sql);

	$leg1 = array();
	$leg2 = array();
	$leg3 = array();
	$leg4 = array();
	$leg5 = array();
	$leg6 = array();
	$leg7 = array();
	$leg8 = array();
	$leg9 = array();
	$leg10 = array();

	$leg11 = array();
	$leg12 = array();
	$leg13 = array();
	$leg14 = array();
	$leg15 = array();
	$leg16 = array();
	$leg17 = array();
	$leg18 = array();
	$leg19 = array();
	$leg20 = array();

	$leg21 = array();
	$leg22 = array();
	$leg23 = array();
	$leg24 = array();
	$leg25 = array();
	$leg26 = array();
	$leg27 = array();
	$leg28 = array();
	$leg29 = array();
	$leg30 = array();

	$leg31 = array();

	$leg1_pk = array();
	$leg2_pk = array();
	$leg3_pk = array();
	$leg4_pk = array();
	$leg5_pk = array();
	$leg6_pk = array();
	$leg7_pk = array();
	$leg8_pk = array();
	$leg9_pk = array();
	$leg10_pk = array();

	$leg11_pk = array();
	$leg12_pk = array();
	$leg13_pk = array();
	$leg14_pk = array();
	$leg15_pk = array();
	$leg16_pk = array();
	$leg17_pk = array();
	$leg18_pk = array();
	$leg19_pk = array();
	$leg20_pk = array();

	$leg21_pk = array();
	$leg22_pk = array();
	$leg23_pk = array();
	$leg24_pk = array();
	$leg25_pk = array();
	$leg26_pk = array();
	$leg27_pk = array();
	$leg28_pk = array();
	$leg29_pk = array();
	$leg30_pk = array();

	$leg31_pk = array();

	$leg_cnt = 0;

		for($j=0; $row=sql_fetch_array($result); $j++) {
			$leg1[] = $row['idx'];
			if ($row['ucertify'] && $row['upackage'] == 'P1') $leg1_pk[] = 1000;
			else if ($row['ucertify'] && $row['upackage'] == 'P2') $leg1_pk[] = 3000;
			else if ($row['ucertify'] && $row['upackage'] == 'P3') $leg1_pk[] = 5000;
			else if ($row['ucertify'] && $row['upackage'] == 'P4') $leg1_pk[] = 10000;
			else $leg1_pk[] = 0;
		}

		for($i=0; $i<count($leg1); $i++) {
			$sql = " select idx, upackage, ucertify
					from {$tara['member_table']}
					where urecommender = '$leg1[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg2[] = $row['idx'];
				if ($row['ucertify'] && $row['upackage'] == 'P1') $leg2_pk[] = 1000;
				else if ($row['ucertify'] && $row['upackage'] == 'P2') $leg2_pk[] = 3000;
				else if ($row['ucertify'] && $row['upackage'] == 'P3') $leg2_pk[] = 5000;
				else if ($row['ucertify'] && $row['upackage'] == 'P4') $leg2_pk[] = 10000;
				else $leg2_pk[] = 0;
			}
		}

		for($i=0; $i<count($leg2); $i++) {
			$sql = " select idx, upackage, ucertify
					from {$tara['member_table']}
					where urecommender = '$leg2[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg3[] = $row['idx'];
				if ($row['ucertify'] && $row['upackage'] == 'P1') $leg3_pk[] = 1000;
				else if ($row['ucertify'] && $row['upackage'] == 'P2') $leg3_pk[] = 3000;
				else if ($row['ucertify'] && $row['upackage'] == 'P3') $leg3_pk[] = 5000;
				else if ($row['ucertify'] && $row['upackage'] == 'P4') $leg3_pk[] = 10000;
				else $leg3_pk[] = 0;
			}
		}

		for($i=0; $i<count($leg3); $i++) {
			$sql = " select idx, upackage, ucertify
					from {$tara['member_table']}
					where urecommender = '$leg3[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg4[] = $row['idx'];
				if ($row['ucertify'] && $row['upackage'] == 'P1') $leg4_pk[] = 1000;
				else if ($row['ucertify'] && $row['upackage'] == 'P2') $leg4_pk[] = 3000;
				else if ($row['ucertify'] && $row['upackage'] == 'P3') $leg4_pk[] = 5000;
				else if ($row['ucertify'] && $row['upackage'] == 'P4') $leg4_pk[] = 10000;
				else $leg4_pk[] = 0;
			}
		}

		for($i=0; $i<count($leg4); $i++) {
			$sql = " select idx, upackage, ucertify
					from {$tara['member_table']}
					where urecommender = '$leg4[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg5[] = $row['idx'];
				if ($row['ucertify'] && $row['upackage'] == 'P1') $leg5_pk[] = 1000;
				else if ($row['ucertify'] && $row['upackage'] == 'P2') $leg5_pk[] = 3000;
				else if ($row['ucertify'] && $row['upackage'] == 'P3') $leg5_pk[] = 5000;
				else if ($row['ucertify'] && $row['upackage'] == 'P4') $leg5_pk[] = 10000;
				else $leg5_pk[] = 0;
			}
		}

		for($i=0; $i<count($leg5); $i++) {
			$sql = " select idx, upackage, ucertify
					from {$tara['member_table']}
					where urecommender = '$leg5[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg6[] = $row['idx'];
				if ($row['ucertify'] && $row['upackage'] == 'P1') $leg6_pk[] = 1000;
				else if ($row['ucertify'] && $row['upackage'] == 'P2') $leg6_pk[] = 3000;
				else if ($row['ucertify'] && $row['upackage'] == 'P3') $leg6_pk[] = 5000;
				else if ($row['ucertify'] && $row['upackage'] == 'P4') $leg6_pk[] = 10000;
				else $leg6_pk[] = 0;
			}
		}

		for($i=0; $i<count($leg6); $i++) {
			$sql = " select idx, upackage, ucertify
					from {$tara['member_table']}
					where urecommender = '$leg6[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg7[] = $row['idx'];
				if ($row['ucertify'] && $row['upackage'] == 'P1') $leg7_pk[] = 1000;
				else if ($row['ucertify'] && $row['upackage'] == 'P2') $leg7_pk[] = 3000;
				else if ($row['ucertify'] && $row['upackage'] == 'P3') $leg7_pk[] = 5000;
				else if ($row['ucertify'] && $row['upackage'] == 'P4') $leg7_pk[] = 10000;
				else $leg7_pk[] = 0;
			}
		}

		for($i=0; $i<count($leg7); $i++) {
			$sql = " select idx, upackage, ucertify
					from {$tara['member_table']}
					where urecommender = '$leg7[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg8[] = $row['idx'];
				if ($row['ucertify'] && $row['upackage'] == 'P1') $leg8_pk[] = 1000;
				else if ($row['ucertify'] && $row['upackage'] == 'P2') $leg8_pk[] = 3000;
				else if ($row['ucertify'] && $row['upackage'] == 'P3') $leg8_pk[] = 5000;
				else if ($row['ucertify'] && $row['upackage'] == 'P4') $leg8_pk[] = 10000;
				else $leg8_pk[] = 0;
			}
		}

		for($i=0; $i<count($leg8); $i++) {
			$sql = " select idx, upackage, ucertify
					from {$tara['member_table']}
					where urecommender = '$leg8[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg9[] = $row['idx'];
				if ($row['ucertify'] && $row['upackage'] == 'P1') $leg9_pk[] = 1000;
				else if ($row['ucertify'] && $row['upackage'] == 'P2') $leg9_pk[] = 3000;
				else if ($row['ucertify'] && $row['upackage'] == 'P3') $leg9_pk[] = 5000;
				else if ($row['ucertify'] && $row['upackage'] == 'P4') $leg9_pk[] = 10000;
				else $leg9_pk[] = 0;
			}
		}

		for($i=0; $i<count($leg9); $i++) {
			$sql = " select idx, upackage, ucertify
					from {$tara['member_table']}
					where urecommender = '$leg9[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg10[] = $row['idx'];
				if ($row['ucertify'] && $row['upackage'] == 'P1') $leg10_pk[] = 1000;
				else if ($row['ucertify'] && $row['upackage'] == 'P2') $leg10_pk[] = 3000;
				else if ($row['ucertify'] && $row['upackage'] == 'P3') $leg10_pk[] = 5000;
				else if ($row['ucertify'] && $row['upackage'] == 'P4') $leg10_pk[] = 10000;
				else $leg10_pk[] = 0;
			}
		}

		for($i=0; $i<count($leg10); $i++) {
			$sql = " select idx, upackage, ucertify
					from {$tara['member_table']}
					where urecommender = '$leg10[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg11[] = $row['idx'];
				if ($row['ucertify'] && $row['upackage'] == 'P1') $leg11_pk[] = 1000;
				else if ($row['ucertify'] && $row['upackage'] == 'P2') $leg11_pk[] = 3000;
				else if ($row['ucertify'] && $row['upackage'] == 'P3') $leg11_pk[] = 5000;
				else if ($row['ucertify'] && $row['upackage'] == 'P4') $leg11_pk[] = 10000;
				else $leg11_pk[] = 0;
			}
		}

		for($i=0; $i<count($leg11); $i++) {
			$sql = " select idx, upackage, ucertify
					from {$tara['member_table']}
					where urecommender = '$leg11[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg12[] = $row['idx'];
				if ($row['ucertify'] && $row['upackage'] == 'P1') $leg12_pk[] = 1000;
				else if ($row['ucertify'] && $row['upackage'] == 'P2') $leg12_pk[] = 3000;
				else if ($row['ucertify'] && $row['upackage'] == 'P3') $leg12_pk[] = 5000;
				else if ($row['ucertify'] && $row['upackage'] == 'P4') $leg12_pk[] = 10000;
				else $leg12_pk[] = 0;
			}
		}

		for($i=0; $i<count($leg12); $i++) {
			$sql = " select idx, upackage, ucertify
					from {$tara['member_table']}
					where urecommender = '$leg12[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg13[] = $row['idx'];
				if ($row['ucertify'] && $row['upackage'] == 'P1') $leg13_pk[] = 1000;
				else if ($row['ucertify'] && $row['upackage'] == 'P2') $leg13_pk[] = 3000;
				else if ($row['ucertify'] && $row['upackage'] == 'P3') $leg13_pk[] = 5000;
				else if ($row['ucertify'] && $row['upackage'] == 'P4') $leg13_pk[] = 10000;
				else $leg13_pk[] = 0;
			}
		}

		for($i=0; $i<count($leg13); $i++) {
			$sql = " select idx, upackage, ucertify
					from {$tara['member_table']}
					where urecommender = '$leg13[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg14[] = $row['idx'];
				if ($row['ucertify'] && $row['upackage'] == 'P1') $leg14_pk[] = 1000;
				else if ($row['ucertify'] && $row['upackage'] == 'P2') $leg14_pk[] = 3000;
				else if ($row['ucertify'] && $row['upackage'] == 'P3') $leg14_pk[] = 5000;
				else if ($row['ucertify'] && $row['upackage'] == 'P4') $leg14_pk[] = 10000;
				else $leg14_pk[] = 0;
			}
		}

		for($i=0; $i<count($leg14); $i++) {
			$sql = " select idx, upackage, ucertify
					from {$tara['member_table']}
					where urecommender = '$leg14[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg15[] = $row['idx'];
				if ($row['ucertify'] && $row['upackage'] == 'P1') $leg15_pk[] = 1000;
				else if ($row['ucertify'] && $row['upackage'] == 'P2') $leg15_pk[] = 3000;
				else if ($row['ucertify'] && $row['upackage'] == 'P3') $leg15_pk[] = 5000;
				else if ($row['ucertify'] && $row['upackage'] == 'P4') $leg15_pk[] = 10000;
				else $leg15_pk[] = 0;
			}
		}

		for($i=0; $i<count($leg15); $i++) {
			$sql = " select idx, upackage, ucertify
					from {$tara['member_table']}
					where urecommender = '$leg15[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg16[] = $row['idx'];
				if ($row['ucertify'] && $row['upackage'] == 'P1') $leg16_pk[] = 1000;
				else if ($row['ucertify'] && $row['upackage'] == 'P2') $leg16_pk[] = 3000;
				else if ($row['ucertify'] && $row['upackage'] == 'P3') $leg16_pk[] = 5000;
				else if ($row['ucertify'] && $row['upackage'] == 'P4') $leg16_pk[] = 10000;
				else $leg16_pk[] = 0;
			}
		}

		for($i=0; $i<count($leg16); $i++) {
			$sql = " select idx, upackage, ucertify
					from {$tara['member_table']}
					where urecommender = '$leg16[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg17[] = $row['idx'];
				if ($row['ucertify'] && $row['upackage'] == 'P1') $leg17_pk[] = 1000;
				else if ($row['ucertify'] && $row['upackage'] == 'P2') $leg17_pk[] = 3000;
				else if ($row['ucertify'] && $row['upackage'] == 'P3') $leg17_pk[] = 5000;
				else if ($row['ucertify'] && $row['upackage'] == 'P4') $leg17_pk[] = 10000;
				else $leg17_pk[] = 0;
			}
		}

		for($i=0; $i<count($leg17); $i++) {
			$sql = " select idx, upackage, ucertify
					from {$tara['member_table']}
					where urecommender = '$leg17[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg18[] = $row['idx'];
				if ($row['ucertify'] && $row['upackage'] == 'P1') $leg18_pk[] = 1000;
				else if ($row['ucertify'] && $row['upackage'] == 'P2') $leg18_pk[] = 3000;
				else if ($row['ucertify'] && $row['upackage'] == 'P3') $leg18_pk[] = 5000;
				else if ($row['ucertify'] && $row['upackage'] == 'P4') $leg18_pk[] = 10000;
				else $leg18_pk[] = 0;
			}
		}

		for($i=0; $i<count($leg18); $i++) {
			$sql = " select idx, upackage, ucertify
					from {$tara['member_table']}
					where urecommender = '$leg18[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg19[] = $row['idx'];
				if ($row['ucertify'] && $row['upackage'] == 'P1') $leg19_pk[] = 1000;
				else if ($row['ucertify'] && $row['upackage'] == 'P2') $leg19_pk[] = 3000;
				else if ($row['ucertify'] && $row['upackage'] == 'P3') $leg19_pk[] = 5000;
				else if ($row['ucertify'] && $row['upackage'] == 'P4') $leg19_pk[] = 10000;
				else $leg19_pk[] = 0;
			}
		}

		for($i=0; $i<count($leg19); $i++) {
			$sql = " select idx, upackage, ucertify
					from {$tara['member_table']}
					where urecommender = '$leg19[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg20[] = $row['idx'];
				if ($row['ucertify'] && $row['upackage'] == 'P1') $leg20_pk[] = 1000;
				else if ($row['ucertify'] && $row['upackage'] == 'P2') $leg20_pk[] = 3000;
				else if ($row['ucertify'] && $row['upackage'] == 'P3') $leg20_pk[] = 5000;
				else if ($row['ucertify'] && $row['upackage'] == 'P4') $leg20_pk[] = 10000;
				else $leg20_pk[] = 0;
			}
		}

		for($i=0; $i<count($leg20); $i++) {
			$sql = " select idx, upackage, ucertify
					from {$tara['member_table']}
					where urecommender = '$leg20[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg21[] = $row['idx'];
				if ($row['ucertify'] && $row['upackage'] == 'P1') $leg21_pk[] = 1000;
				else if ($row['ucertify'] && $row['upackage'] == 'P2') $leg21_pk[] = 3000;
				else if ($row['ucertify'] && $row['upackage'] == 'P3') $leg21_pk[] = 5000;
				else if ($row['ucertify'] && $row['upackage'] == 'P4') $leg21_pk[] = 10000;
				else $leg21_pk[] = 0;
			}
		}

		for($i=0; $i<count($leg21); $i++) {
			$sql = " select idx, upackage, ucertify
					from {$tara['member_table']}
					where urecommender = '$leg21[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg22[] = $row['idx'];
				if ($row['ucertify'] && $row['upackage'] == 'P1') $leg22_pk[] = 1000;
				else if ($row['ucertify'] && $row['upackage'] == 'P2') $leg22_pk[] = 3000;
				else if ($row['ucertify'] && $row['upackage'] == 'P3') $leg22_pk[] = 5000;
				else if ($row['ucertify'] && $row['upackage'] == 'P4') $leg22_pk[] = 10000;
				else $leg22_pk[] = 0;
			}
		}

		for($i=0; $i<count($leg22); $i++) {
			$sql = " select idx, upackage, ucertify
					from {$tara['member_table']}
					where urecommender = '$leg22[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg23[] = $row['idx'];
				if ($row['ucertify'] && $row['upackage'] == 'P1') $leg23_pk[] = 1000;
				else if ($row['ucertify'] && $row['upackage'] == 'P2') $leg23_pk[] = 3000;
				else if ($row['ucertify'] && $row['upackage'] == 'P3') $leg23_pk[] = 5000;
				else if ($row['ucertify'] && $row['upackage'] == 'P4') $leg23_pk[] = 10000;
				else $leg23_pk[] = 0;
			}
		}

		for($i=0; $i<count($leg23); $i++) {
			$sql = " select idx, upackage, ucertify
					from {$tara['member_table']}
					where urecommender = '$leg23[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg24[] = $row['idx'];
				if ($row['ucertify'] && $row['upackage'] == 'P1') $leg24_pk[] = 1000;
				else if ($row['ucertify'] && $row['upackage'] == 'P2') $leg24_pk[] = 3000;
				else if ($row['ucertify'] && $row['upackage'] == 'P3') $leg24_pk[] = 5000;
				else if ($row['ucertify'] && $row['upackage'] == 'P4') $leg24_pk[] = 10000;
				else $leg24_pk[] = 0;
			}
		}

		for($i=0; $i<count($leg24); $i++) {
			$sql = " select idx, upackage, ucertify
					from {$tara['member_table']}
					where urecommender = '$leg24[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg25[] = $row['idx'];
				if ($row['ucertify'] && $row['upackage'] == 'P1') $leg25_pk[] = 1000;
				else if ($row['ucertify'] && $row['upackage'] == 'P2') $leg25_pk[] = 3000;
				else if ($row['ucertify'] && $row['upackage'] == 'P3') $leg25_pk[] = 5000;
				else if ($row['ucertify'] && $row['upackage'] == 'P4') $leg25_pk[] = 10000;
				else $leg25_pk[] = 0;
			}
		}

		for($i=0; $i<count($leg25); $i++) {
			$sql = " select idx, upackage, ucertify
					from {$tara['member_table']}
					where urecommender = '$leg25[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg26[] = $row['idx'];
				if ($row['ucertify'] && $row['upackage'] == 'P1') $leg26_pk[] = 1000;
				else if ($row['ucertify'] && $row['upackage'] == 'P2') $leg26_pk[] = 3000;
				else if ($row['ucertify'] && $row['upackage'] == 'P3') $leg26_pk[] = 5000;
				else if ($row['ucertify'] && $row['upackage'] == 'P4') $leg26_pk[] = 10000;
				else $leg26_pk[] = 0;
			}
		}

		for($i=0; $i<count($leg26); $i++) {
			$sql = " select idx, upackage, ucertify
					from {$tara['member_table']}
					where urecommender = '$leg26[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg27[] = $row['idx'];
				if ($row['ucertify'] && $row['upackage'] == 'P1') $leg27_pk[] = 1000;
				else if ($row['ucertify'] && $row['upackage'] == 'P2') $leg27_pk[] = 3000;
				else if ($row['ucertify'] && $row['upackage'] == 'P3') $leg27_pk[] = 5000;
				else if ($row['ucertify'] && $row['upackage'] == 'P4') $leg27_pk[] = 10000;
				else $leg27_pk[] = 0;
			}
		}

		for($i=0; $i<count($leg27); $i++) {
			$sql = " select idx, upackage, ucertify
					from {$tara['member_table']}
					where urecommender = '$leg27[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg28[] = $row['idx'];
				if ($row['ucertify'] && $row['upackage'] == 'P1') $leg28_pk[] = 1000;
				else if ($row['ucertify'] && $row['upackage'] == 'P2') $leg28_pk[] = 3000;
				else if ($row['ucertify'] && $row['upackage'] == 'P3') $leg28_pk[] = 5000;
				else if ($row['ucertify'] && $row['upackage'] == 'P4') $leg28_pk[] = 10000;
				else $leg28_pk[] = 0;
			}
		}

		for($i=0; $i<count($leg28); $i++) {
			$sql = " select idx, upackage, ucertify
					from {$tara['member_table']}
					where urecommender = '$leg28[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg29[] = $row['idx'];
				if ($row['ucertify'] && $row['upackage'] == 'P1') $leg29_pk[] = 1000;
				else if ($row['ucertify'] && $row['upackage'] == 'P2') $leg29_pk[] = 3000;
				else if ($row['ucertify'] && $row['upackage'] == 'P3') $leg29_pk[] = 5000;
				else if ($row['ucertify'] && $row['upackage'] == 'P4') $leg29_pk[] = 10000;
				else $leg29_pk[] = 0;
			}
		}

		for($i=0; $i<count($leg29); $i++) {
			$sql = " select idx, upackage, ucertify
					from {$tara['member_table']}
					where urecommender = '$leg29[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg30[] = $row['idx'];
				if ($row['ucertify'] && $row['upackage'] == 'P1') $leg30_pk[] = 1000;
				else if ($row['ucertify'] && $row['upackage'] == 'P2') $leg30_pk[] = 3000;
				else if ($row['ucertify'] && $row['upackage'] == 'P3') $leg30_pk[] = 5000;
				else if ($row['ucertify'] && $row['upackage'] == 'P4') $leg30_pk[] = 10000;
				else $leg30_pk[] = 0;
			}
		}

		for($i=0; $i<count($leg30); $i++) {
			$sql = " select idx, upackage, ucertify
					from {$tara['member_table']}
					where urecommender = '$leg30[$i]'
					$sql_order ";
			$result = sql_query($sql);

			for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg31[] = $row['idx'];
				if ($row['ucertify'] && $row['upackage'] == 'P1') $leg31_pk[] = 1000;
				else if ($row['ucertify'] && $row['upackage'] == 'P2') $leg31_pk[] = 3000;
				else if ($row['ucertify'] && $row['upackage'] == 'P3') $leg31_pk[] = 5000;
				else if ($row['ucertify'] && $row['upackage'] == 'P4') $leg31_pk[] = 10000;
				else $leg31_pk[] = 0;
			}
		}

	$leg_cnt = array_sum($leg1_pk)+array_sum($leg2_pk)+array_sum($leg3_pk)+array_sum($leg4_pk)+array_sum($leg5_pk)+array_sum($leg6_pk)+array_sum($leg7_pk)+array_sum($leg8_pk)+array_sum($leg9_pk)+array_sum($leg10_pk)+array_sum($leg11_pk)+array_sum($leg12_pk)+array_sum($leg13_pk)+array_sum($leg14_pk)+array_sum($leg15_pk)+array_sum($leg16_pk)+array_sum($leg17_pk)+array_sum($leg18_pk)+array_sum($leg19_pk)+array_sum($leg20_pk)+array_sum($leg21_pk)+array_sum($leg22_pk)+array_sum($leg23_pk)+array_sum($leg24_pk)+array_sum($leg25_pk)+array_sum($leg26_pk)+array_sum($leg27_pk)+array_sum($leg28_pk)+array_sum($leg29_pk)+array_sum($leg30_pk)+array_sum($leg31_pk);

	return $leg_cnt;
}


function get_position_leg1($mb_no)
{
	global $tara;

    $sql_order = " order by idx asc ";

    $sql = " select idx
                from {$tara['member_table']}
                where urecommender = '$mb_no'
                $sql_order ";
    $result = sql_query($sql);

	$leg1 = array();

	$leg1_pk = array();

	$leg_cnt = 0;

	for($j=0; $row=sql_fetch_array($result); $j++) {
			$leg1[] = $row['idx'];
	}

	for($i=0; $i<count($leg1); $i++) {

		$posit_sales = get_sales_num($leg1[$i]);

		if ($posit_sales >= 100000) $leg1_pk[] = $leg1[$i];

	}

	$leg_cnt = count($leg1_pk);

	return $leg_cnt;
}


function get_position_leg2($mb_no)
{
	global $tara;

    $sql_order = " order by idx asc ";

    $sql = " select idx
                from {$tara['member_table']}
                where urecommender = '$mb_no'
                $sql_order ";
    $result = sql_query($sql);

	$leg1 = array();
	$leg2 = array();

	$leg2_pk = array();

	$leg_cnt = 0;

	for($j=0; $row=sql_fetch_array($result); $j++) {
			$leg1[] = $row['idx'];
	}

	for($i=0; $i<count($leg1); $i++) {

		$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg1[$i]'
					$sql_order ";
		$result = sql_query($sql);

		for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg2[] = $row['idx'];
		}
	}

	for($i=0; $i<count($leg2); $i++) {

		$posit_sales = get_sales_num($leg2[$i]);

		if ($posit_sales >= 100000) $leg2_pk[] = $leg2[$i];

	}

	$leg_cnt = count($leg2_pk);

	return $leg_cnt;
}


function get_position_leg3($mb_no)
{
	global $tara;

    $sql_order = " order by idx asc ";

    $sql = " select idx
                from {$tara['member_table']}
                where urecommender = '$mb_no'
                $sql_order ";
    $result = sql_query($sql);

	$leg1 = array();
	$leg2 = array();
	$leg3 = array();

	$leg3_pk = array();

	$leg_cnt = 0;

	for($j=0; $row=sql_fetch_array($result); $j++) {
			$leg1[] = $row['idx'];
	}

	for($i=0; $i<count($leg1); $i++) {

		$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg1[$i]'
					$sql_order ";
		$result = sql_query($sql);

		for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg2[] = $row['idx'];
		}
	}

	for($i=0; $i<count($leg2); $i++) {

		$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg2[$i]'
					$sql_order ";
		$result = sql_query($sql);

		for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg3[] = $row['idx'];
		}
	}

	for($i=0; $i<count($leg3); $i++) {

		$posit_sales = get_sales_num($leg3[$i]);

		if ($posit_sales >= 100000) $leg3_pk[] = $leg3[$i];

	}

	$leg_cnt = count($leg3_pk);

	return $leg_cnt;
}


function get_position_1d($mb_no)
{

	$posit_label = '';
	$posit_sales = get_sales_num($mb_no);

	if ($posit_sales >= 100000) $posit_label = '1D';

	return $posit_label;

}


function get_position_2d($mb_no)
{

	$posit_label = '';
	$posit_leg1 = get_position_leg1($mb_no);
	$posit_leg2 = get_position_leg2($mb_no);
	$posit_leg3 = get_position_leg3($mb_no);

	if ($posit_leg1 >= 1 && $posit_leg2 >= 1 && $posit_leg3 >= 1) $posit_label = '2D';

	return $posit_label;

}


function get_position_3d($mb_no)
{

	global $tara;

	$posit_label = '';

    $sql_order = " order by idx asc ";

    $sql = " select idx
                from {$tara['member_table']}
                where urecommender = '$mb_no'
                $sql_order ";
    $result = sql_query($sql);

	$leg1 = array();
	$leg2 = array();
	$leg3 = array();

	$leg1_cnt = 0;
	$leg2_cnt = 0;
	$leg3_cnt = 0;

	$leg_cnt = 0;

	for($j=0; $row=sql_fetch_array($result); $j++) {
			$leg1[] = $row['idx'];
	}

	for($i=0; $i<count($leg1); $i++) {

		$posit_leg = get_position_2d($leg1[$i]);

		if ($posit_leg == '2D') $leg1_cnt = $leg1_cnt + 1;

		$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg1[$i]'
					$sql_order ";
		$result = sql_query($sql);

		for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg2[] = $row['idx'];
		}

	}

	for($i=0; $i<count($leg2); $i++) {

		$posit_leg = get_position_2d($leg2[$i]);

		if ($posit_leg == '2D') $leg2_cnt = $leg2_cnt + 1;

		$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg2[$i]'
					$sql_order ";
		$result = sql_query($sql);

		for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg3[] = $row['idx'];
		}
	}

	for($i=0; $i<count($leg3); $i++) {

		$posit_leg = get_position_2d($leg3[$i]);

		if ($posit_leg == '2D') $leg3_cnt = $leg3_cnt + 1;

	}

	if ($leg1_cnt >= 2 && $leg2_cnt >= 1 && $leg3_cnt >= 1) $posit_label = '3D';

	return $posit_label;

}


function get_position_4d($mb_no)
{

	global $tara;

	$posit_label = '';

    $sql_order = " order by idx asc ";

    $sql = " select idx
                from {$tara['member_table']}
                where urecommender = '$mb_no'
                $sql_order ";
    $result = sql_query($sql);

	$leg1 = array();
	$leg2 = array();
	$leg3 = array();

	$leg1_cnt = 0;
	$leg2_cnt = 0;
	$leg3_cnt = 0;

	$leg_cnt = 0;

	for($j=0; $row=sql_fetch_array($result); $j++) {
			$leg1[] = $row['idx'];
	}

	for($i=0; $i<count($leg1); $i++) {

		$posit_leg = get_position_3d($leg1[$i]);

		if ($posit_leg == '3D') $leg1_cnt = $leg1_cnt + 1;

		$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg1[$i]'
					$sql_order ";
		$result = sql_query($sql);

		for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg2[] = $row['idx'];
		}

	}

	for($i=0; $i<count($leg2); $i++) {

		$posit_leg = get_position_3d($leg2[$i]);

		if ($posit_leg == '3D') $leg2_cnt = $leg2_cnt + 1;

		$sql = " select idx
					from {$tara['member_table']}
					where urecommender = '$leg2[$i]'
					$sql_order ";
		$result = sql_query($sql);

		for($j=0; $row=sql_fetch_array($result); $j++) {
				$leg3[] = $row['idx'];
		}
	}

	for($i=0; $i<count($leg3); $i++) {

		$posit_leg = get_position_3d($leg3[$i]);

		if ($posit_leg == '3D') $leg3_cnt = $leg3_cnt + 1;

	}

	if ($leg1_cnt >= 2 && $leg2_cnt >= 1 && $leg3_cnt >= 1) $posit_label = '4D';

	return $posit_label;

}


function get_position_num($mb_no)
{

	$posit_label = '';
	$posit_1d = get_position_1d($mb_no);
	$posit_2d = get_position_2d($mb_no);
	$posit_3d = get_position_3d($mb_no);
	$posit_4d = get_position_4d($mb_no);


	if ($posit_4d == '4D') $posit_label = 'C D';
	else if ($posit_3d == '3D') $posit_label = '3 D';
	else if ($posit_2d == '2D') $posit_label = '2 D';
	else if ($posit_1d == '1D') $posit_label = '1 D';

	return $posit_label;
}


// 메타태그를 이용한 URL 이동
// header("location:URL") 을 대체
function goto_url($url)
{
    $url = str_replace("&amp;", "&", $url);
    //echo "<script> location.replace('$url'); </script>";

    if (!headers_sent())
        header('Location: '.$url);
    else {
        echo '<script>';
        echo 'location.replace("'.$url.'");';
        echo '</script>';
        echo '<noscript>';
        echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
        echo '</noscript>';
    }
    exit;
}


// 경고메세지를 경고창으로
function alert($msg='', $url='', $error=true, $post=false)
{
    global $tara, $config, $member;
    global $is_admin;

    $msg = $msg ? strip_tags($msg, '<br>') : '올바른 방법으로 이용해 주십시오.';

    $header = '';
    if (isset($tara['title'])) {
        $header = $tara['title'];
    }
    include_once('./alert.php');
    exit;
}


// 경고메세지 출력후 창을 닫음
function alert_close($msg, $error=true)
{
    global $tara;

    $msg = strip_tags($msg, '<br>');

    $header = '';
    if (isset($tara['title'])) {
        $header = $tara['title'];
    }
    include_once('./alert_close.php');
    exit;
}


// confirm 창
function confirm($msg, $url1='', $url2='', $url3='')
{
    global $tara;

    if (!$msg) {
        $msg = '올바른 방법으로 이용해 주십시오.';
        alert($msg);
    }

    if(!trim($url1) || !trim($url2)) {
        $msg = '$url1 과 $url2 를 지정해 주세요.';
        alert($msg);
    }

    if (!$url3) $url3 = clean_xss_tags($_SERVER['HTTP_REFERER']);

    $msg = str_replace("\\n", "<br>", $msg);

    $header = '';
    if (isset($tara['title'])) {
        $header = $tara['title'];
    }
    include_once('./confirm.php');
    exit;
}


// 동일한 host url 인지
function check_url_host($url, $msg='', $return_url='/')
{
    if(!$msg)
        $msg = 'url에 타 도메인을 지정할 수 없습니다.';

    $p = @parse_url($url);
    $host = preg_replace('/:[0-9]+$/', '', $_SERVER['HTTP_HOST']);
    $is_host_check = false;

    if(stripos($url, 'http:') !== false) {
        if(!isset($p['scheme']) || !$p['scheme'] || !isset($p['host']) || !$p['host'])
            alert('url 정보가 올바르지 않습니다.', $return_url);
    }

    //php 5.6.29 이하 버전에서는 parse_url 버그가 존재함
    if ( (isset($p['host']) && $p['host']) && version_compare(PHP_VERSION, '5.6.29') < 0) {
        $bool_ch = false;
        foreach( array('user','host') as $key) {
            if ( isset( $p[ $key ] ) && strpbrk( $p[ $key ], ':/?#@' ) ) {
                $bool_ch = true;
            }
        }
        if( $bool_ch ){
            $regex = '/https?\:\/\/'.$host.'/i';
            if( ! preg_match($regex, $url) ){
                $is_host_check = true;
            }
        }
    }

    if ((isset($p['scheme']) && $p['scheme']) || (isset($p['host']) && $p['host']) || $is_host_check) {
        //if ($p['host'].(isset($p['port']) ? ':'.$p['port'] : '') != $_SERVER['HTTP_HOST']) {
        if ( ($p['host'] != $host) || $is_host_check ) {
            echo '<script>'.PHP_EOL;
            echo 'alert("url에 타 도메인을 지정할 수 없습니다.");'.PHP_EOL;
            echo 'document.location.href = "'.$return_url.'";'.PHP_EOL;
            echo '</script>'.PHP_EOL;
            echo '<noscript>'.PHP_EOL;
            echo '<p>'.$msg.'</p>'.PHP_EOL;
            echo '<p><a href="'.$return_url.'">돌아가기</a></p>'.PHP_EOL;
            echo '</noscript>'.PHP_EOL;
            exit;
        }
    }
}


// TEXT 형식으로 변환
function get_text($str, $html=0, $restore=false)
{
    $source[] = "<";
    $target[] = "&lt;";
    $source[] = ">";
    $target[] = "&gt;";
    $source[] = "\"";
    $target[] = "&#034;";
    $source[] = "\'";
    $target[] = "&#039;";

    if($restore)
        $str = str_replace($target, $source, $str);

    // 3.31
    // TEXT 출력일 경우 &amp; &nbsp; 등의 코드를 정상으로 출력해 주기 위함
    if ($html == 0) {
        $str = html_symbol($str);
    }

    if ($html) {
        $source[] = "\n";
        $target[] = "<br/>";
    }

    return str_replace($source, $target, $str);
}


// 3.31
// HTML SYMBOL 변환
// &nbsp; &amp; &middot; 등을 정상으로 출력
function html_symbol($str)
{
    return preg_replace("/\&([a-z0-9]{1,20}|\#[0-9]{0,3});/i", "&#038;\\1;", $str);
}


// 검색어 특수문자 제거
function get_search_string($stx)
{
    $stx_pattern = array();
    $stx_pattern[] = '#\.*/+#';
    $stx_pattern[] = '#\\\*#';
    $stx_pattern[] = '#\.{2,}#';
    $stx_pattern[] = '#[/\'\"%=*\#\(\)\|\+\&\!\$~\{\}\[\]`;:\?\^\,]+#';

    $stx_replace = array();
    $stx_replace[] = '';
    $stx_replace[] = '';
    $stx_replace[] = '.';
    $stx_replace[] = '';

    $stx = preg_replace($stx_pattern, $stx_replace, $stx);

    return $stx;
}


function cut_str($str, $len, $suffix="…")
{
    $arr_str = preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
    $str_len = count($arr_str);

    if ($str_len >= $len) {
        $slice_str = array_slice($arr_str, 0, $len);
        $str = join("", $slice_str);

        return $str . ($str_len > $len ? $suffix : '');
    } else {
        $str = join("", $arr_str);
        return $str;
    }
}


function get_selected($field, $value)
{
    return ($field==$value) ? ' selected="selected"' : '';
}


function get_checked($field, $value)
{
    return ($field==$value) ? ' checked="checked"' : '';
}

// 날짜, 조회수의 경우 높은 순서대로 보여져야 하므로 $flag 를 추가
// $flag : asc 낮은 순서 , desc 높은 순서
// 제목별로 컬럼 정렬하는 QUERY STRING
function subject_sort_link($col, $query_string='', $flag='asc')
{
    global $sst, $sod, $sfl, $stx, $page, $sca;

    $q1 = "sst=$col";
    if ($flag == 'asc')
    {
        $q2 = 'sod=asc';
        if ($sst == $col)
        {
            if ($sod == 'asc')
            {
                $q2 = 'sod=desc';
            }
        }
    }
    else
    {
        $q2 = 'sod=desc';
        if ($sst == $col)
        {
            if ($sod == 'desc')
            {
                $q2 = 'sod=asc';
            }
        }
    }

    $arr_query = array();
    $arr_query[] = $query_string;
    $arr_query[] = $q1;
    $arr_query[] = $q2;
    $arr_query[] = 'sfl='.$sfl;
    $arr_query[] = 'stx='.$stx;
    $arr_query[] = 'sca='.$sca;
    $arr_query[] = 'page='.$page;
    $qstr = implode("&amp;", $arr_query);

    return "<a href=\"{$_SERVER['SCRIPT_NAME']}?{$qstr}\">";
}

// 한페이지에 보여줄 행, 현재페이지, 총페이지수, URL
function get_paging($write_pages, $cur_page, $total_page, $url, $add="")
{
    //$url = preg_replace('#&amp;page=[0-9]*(&amp;page=)$#', '$1', $url);
    $url = preg_replace('#&amp;page=[0-9]*#', '', $url) . '&amp;page=';

    $str = '';
    if ($cur_page > 1) {
        $str .= '<a href="'.$url.'1'.$add.'" class="pg_page pg_start">First</a>'.PHP_EOL;
    }

    $start_page = ( ( (int)( ($cur_page - 1 ) / $write_pages ) ) * $write_pages ) + 1;
    $end_page = $start_page + $write_pages - 1;

    if ($end_page >= $total_page) $end_page = $total_page;

    if ($start_page > 1) $str .= '<a href="'.$url.($start_page-1).$add.'" class="pg_page pg_prev">Prev</a>'.PHP_EOL;

    if ($total_page > 1) {
        for ($k=$start_page;$k<=$end_page;$k++) {
            if ($cur_page != $k)
                $str .= '<a href="'.$url.$k.$add.'" class="pg_page">'.$k.'<span class="sound_only">Page</span></a>'.PHP_EOL;
            else
                $str .= '<span class="sound_only">Current</span><strong class="pg_current">'.$k.'</strong><span class="sound_only">Page</span>'.PHP_EOL;
        }
    }

    if ($total_page > $end_page) $str .= '<a href="'.$url.($end_page+1).$add.'" class="pg_page pg_next">Next</a>'.PHP_EOL;

    if ($cur_page < $total_page) {
        $str .= '<a href="'.$url.$total_page.$add.'" class="pg_page pg_end">Last</a>'.PHP_EOL;
    }

    if ($str)
        return "<nav class=\"pg_wrap\"><span class=\"pg\">{$str}</span></nav>";
    else
        return "";
}


/*************************************************************************
**
**  보안 관련 함수 모음
**
*************************************************************************/

// XSS 관련 태그 제거
function clean_xss_tags($str)
{
    $str = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $str);

    return $str;
}

// QUERY STRING 에 포함된 XSS 태그 제거
function clean_query_string($query, $amp=true)
{
    $qstr = trim($query);

    parse_str($qstr, $out);

    if(is_array($out)) {
        $q = array();

        foreach($out as $key=>$val) {
            $key = strip_tags(trim($key));
            $val = trim($val);

            switch($key) {
                case 'wr_id':
                    $val = (int)preg_replace('/[^0-9]/', '', $val);
                    $q[$key] = $val;
                    break;
                case 'sca':
                    $val = clean_xss_tags($val);
                    $q[$key] = $val;
                    break;
                case 'sfl':
                    $val = preg_replace("/[\<\>\'\"\\\'\\\"\%\=\(\)\s]/", "", $val);
                    $q[$key] = $val;
                    break;
                case 'stx':
                    $val = get_search_string($val);
                    $q[$key] = $val;
                    break;
                case 'sst':
                    $val = preg_replace("/[\<\>\'\"\\\'\\\"\%\=\(\)\s]/", "", $val);
                    $q[$key] = $val;
                    break;
                case 'sod':
                    $val = preg_match("/^(asc|desc)$/i", $val) ? $val : '';
                    $q[$key] = $val;
                    break;
                case 'sop':
                    $val = preg_match("/^(or|and)$/i", $val) ? $val : '';
                    $q[$key] = $val;
                    break;
                case 'spt':
                    $val = (int)preg_replace('/[^0-9]/', '', $val);
                    $q[$key] = $val;
                    break;
                case 'page':
                    $val = (int)preg_replace('/[^0-9]/', '', $val);
                    $q[$key] = $val;
                    break;
                case 'w':
                    $val = substr($val, 0, 2);
                    $q[$key] = $val;
                    break;
                case 'bo_table':
                    $val = preg_replace('/[^a-z0-9_]/i', '', $val);
                    $val = substr($val, 0, 20);
                    $q[$key] = $val;
                    break;
                case 'gr_id':
                    $val = preg_replace('/[^a-z0-9_]/i', '', $val);
                    $q[$key] = $val;
                    break;
                default:
                    $val = clean_xss_tags($val);
                    $q[$key] = $val;
                    break;
            }
        }

        if($amp)
            $sep = '&amp;';
        else
            $sep ='&';

        $str = http_build_query($q, '', $sep);
    } else {
        $str = clean_xss_tags($qstr);
    }

    return $str;
}

/*************************************************************************
**
**  SQL 관련 함수 모음
**
*************************************************************************/

// DB 연결
function sql_connect($host, $user, $pass, $db=TARA_MYSQL_DB)
{
    global $tara;

    if(function_exists('mysqli_connect')) {
        $link = mysqli_connect($host, $user, $pass, $db);

        // 연결 오류 발생 시 스크립트 종료
        if (mysqli_connect_errno()) {
            die('Connect Error: '.mysqli_connect_error());
        }
    } else {
        $link = mysql_connect($host, $user, $pass);
    }

    return $link;
}


// DB 선택
function sql_select_db($db, $connect)
{
    global $tara;

    if(function_exists('mysqli_select_db'))
        return @mysqli_select_db($connect, $db);
    else
        return @mysql_select_db($db, $connect);
}


function sql_set_charset($charset, $link=null)
{
    global $tara;

    if(!$link)
        $link = $tara['connect_db'];

    if(function_exists('mysqli_set_charset'))
        mysqli_set_charset($link, $charset);
    else
        mysql_query(" set names {$charset} ", $link);
}


// mysqli_query 와 mysqli_error 를 한꺼번에 처리
function sql_query($sql, $error=TRUE, $link=null)
{
    global $tara;

    if(!$link)
        $link = $tara['connect_db'];

    // Blind SQL Injection 취약점 해결
    $sql = trim($sql);
    // union의 사용을 허락하지 않습니다.
    //$sql = preg_replace("#^select.*from.*union.*#i", "select 1", $sql);
    $sql = preg_replace("#^select.*from.*[\s\(]+union[\s\)]+.*#i ", "select 1", $sql);
    // `information_schema` DB로의 접근을 허락하지 않습니다.
    $sql = preg_replace("#^select.*from.*where.*`?information_schema`?.*#i", "select 1", $sql);

    if(function_exists('mysqli_query')) {
        if ($error) {
            $result = @mysqli_query($link, $sql) or die("<p>$sql<p>" . mysqli_errno($link) . " : " .  mysqli_error($link) . "<p>error file : {$_SERVER['SCRIPT_NAME']}");
        } else {
            $result = @mysqli_query($link, $sql);
        }
    } else {
        if ($error) {
            $result = @mysql_query($sql, $link) or die("<p>$sql<p>" . mysql_errno() . " : " .  mysql_error() . "<p>error file : {$_SERVER['SCRIPT_NAME']}");
        } else {
            $result = @mysql_query($sql, $link);
        }
    }

    return $result;
}


// 쿼리를 실행한 후 결과값에서 한행을 얻는다.
function sql_fetch($sql, $error=TRUE, $link=null)
{
    global $tara;

    if(!$link)
        $link = $tara['connect_db'];

    $result = sql_query($sql, $error, $link);
    //$row = @sql_fetch_array($result) or die("<p>$sql<p>" . mysqli_errno() . " : " .  mysqli_error() . "<p>error file : $_SERVER['SCRIPT_NAME']");
    $row = sql_fetch_array($result);
    return $row;
}


// 결과값에서 한행 연관배열(이름으로)로 얻는다.
function sql_fetch_array($result)
{
    if(function_exists('mysqli_fetch_assoc'))
        $row = @mysqli_fetch_assoc($result);
    else
        $row = @mysql_fetch_assoc($result);

    return $row;
}


// $result에 대한 메모리(memory)에 있는 내용을 모두 제거한다.
// sql_free_result()는 결과로부터 얻은 질의 값이 커서 많은 메모리를 사용할 염려가 있을 때 사용된다.
// 단, 결과 값은 스크립트(script) 실행부가 종료되면서 메모리에서 자동적으로 지워진다.
function sql_free_result($result)
{
    if(function_exists('mysqli_free_result'))
        return mysqli_free_result($result);
    else
        return mysql_free_result($result);
}


function sql_password($value)
{
    // mysql 4.0x 이하 버전에서는 password() 함수의 결과가 16bytes
    // mysql 4.1x 이상 버전에서는 password() 함수의 결과가 41bytes
    $row = sql_fetch(" select password('$value') as pass ");

    return $row['pass'];
}


function sql_insert_id($link=null)
{
    global $tara;

    if(!$link)
        $link = $tara['connect_db'];

    if(function_exists('mysqli_insert_id'))
        return mysqli_insert_id($link);
    else
        return mysql_insert_id($link);
}


function sql_num_rows($result)
{
    if(function_exists('mysqli_num_rows'))
        return mysqli_num_rows($result);
    else
        return mysql_num_rows($result);
}


function sql_field_names($table, $link=null)
{
    global $tara;

    if(!$link)
        $link = $tara['connect_db'];

    $columns = array();

    $sql = " select * from `$table` limit 1 ";
    $result = sql_query($sql, $link);

    if(function_exists('mysqli_fetch_field')) {
        while($field = mysqli_fetch_field($result)) {
            $columns[] = $field->name;
        }
    } else {
        $i = 0;
        $cnt = mysql_num_fields($result);
        while($i < $cnt) {
            $field = mysql_fetch_field($result, $i);
            $columns[] = $field->name;
            $i++;
        }
    }

    return $columns;
}


function sql_error_info($link=null)
{
    global $tara;

    if(!$link)
        $link = $tara['connect_db'];

    if(function_exists('mysqli_error')) {
        return mysqli_errno($link) . ' : ' . mysqli_error($link);
    } else {
        return mysql_errno($link) . ' : ' . mysql_error($link);
    }
}


?>
