<?php
/**
 * 手机号吉凶测试。
 * @author fingerQin
 * @date 2019-08-08
 * 
 * 手机号码吉利数理预测解读：
 * 将手机号码末尾的四个数字，先除以八十，再减去整数部分，只使用剩下的小数（小数点反面的数字）乘以八十，然后将所得结果。
 * 对表查阅，就知道吉凶。（换句话说就是余数）
 * 例如：
 * 手机尾号是 8888,将 8888÷80＝111.1，再将 0.1×80＝8，这个 "8" 就是该手机号码的命运数了，
 * 末了你在命运数对表里不妨查到你是 "吉" 了！ 
 */

namespace Services\Game;

use Utils\YCore;
use finger\Validator;

class MobileLuckyBad extends \Services\AbstractBase
{
    /**
     * 预测结果类型。
     */
    const TYPE_GOOD = 1; // 吉。
    const TYPE_BAD  = 2; // 凶。
    const TYPE_BOTH = 3; // 吉带凶。

    /**
     * 预测结果字典。
     *
     * @var array
     */
    private static $dict = [
		'1'  => '繁荣茂盛，大展鸿图，信用得固，万人渴念，无远弗界，可获告捷 。(吉)',
		'2'  => '摇荡不安，根基不固，风雨飘摇，一荣一枯，一盛一衰，劳而无功 。(凶)',
		'3'  => '立身处世，有贵人助，根深蒂固，日新月异，天赐吉利，百事顺遂，四海扬名 。(吉)',
		'4'  => '日被云遮，凹凸出息，劫难折磨，非有毅力，难望告捷 。(凶)',
		'5'  => '阴阳和合，学习算法。精神痛快，生意欣荣，信用达利，名利双收，一门兴隆，后福重重 。(吉)',
		'6'  => '万宝集门，天降荣幸，立志昂扬，得成大业 。(吉)',
		'7'  => '精神旺盛，头脑明敏，专心规划，良善致祥，消灭万难，必获告捷 。(吉)',
		'8'  => '努力茂盛，贯彻志愿，不忘进退，可期告捷 。(吉)',
		'9'  => '虽抱奇才，有才无命，独营有力，财利难望 。(凶)',
		'10' => '乌云遮月，阴暗无光，空劳神力，白费无功 。(凶)',
		'11' => '草木逢春，枝叶沾露，稳健委实，必得人望 。(吉)',
		'12' => '微弱有力，孤立无摇，外祥内苦，谋事难成 。(凶)',
		'13' => '天赋吉运，能得人望，善用灵巧，必获告捷 。(吉)',
		'14' => '忍得劫难，必有后福，是成是败，惟靠坚定 。(凶)',
		'15' => '傲慢做事，必得人和，小事结果，一门兴隆 。(吉)',
		'16' => '能获众望，结果大业，名利双收，盟主四方 。(吉)',
		'17' => '消灭万难，有贵人助，驾驭时机，可得告捷 。(吉)',
		'18' => '经商做事，亨通昌隆，如能慎始，百事亨通 。(吉)',
		'19' => '告捷虽早，慎防空亏，内外不合，障碍重重 。(凶)',
		'20' => '智高志大，历尽麻烦，焦心忧劳，进退失据 。(凶)',
		'21' => '专心规划，善用灵巧，霜雪梅花，春来怒放 。(吉)',
		'22' => '秋草逢霜，脱颖而出，忧闷怨苦，事不如意 。(凶)',
		'23' => '朝阳升天，名显四方，渐次进展，终成大业 。(吉)',
		'24' => '锦绣前程，须靠自力，多用智谋，能奏大功 。(吉)',
		'25' => '天时天时，只欠人和，讲信修睦，即可告捷 。(吉)',
		'26' => '波涛升沉，变幻莫测，凌驾万难，必可告捷 。(凶)',
		'27' => '一成一败，一盛一衰，惟靠介意，可守告捷 。(凶带吉)',
		'28' => '鱼临旱地，难逃恶运，此数大凶，逢极转运 。(凶)',
		'29' => '如龙得云，扶摇直上，智谋奋进，才略奏功 。(吉)',
		'30' => '吉凶参半，得失相伴，见风转舵，如赛一样 。(凶)',
		'31' => '此数大吉，名利双收，渐进向上，大业结果 。(吉)',
		'32' => '池中之龙，风云际会，一跃上天，告捷可望 。(吉)',
		'33' => '意气用事，人和必失，善用灵巧，如能慎始，必可昌隆 。(吉)',
		'34' => '灾难不绝，难望告捷，此数大凶，逢极转运 。(凶)',
		'35' => '中吉之数，处事稹密，进退守旧，学智兼备，生意稳定，结果不凡 。(吉)',
		'36' => '波涛堆叠，常陷穷困，动不如静，有才无命 。(凶)',
		'37' => '转危为安，善者神佑，风条雨顺，生意兴隆，以德取众，必成大功 。(吉)',
		'38' => '名虽可得，利则难获，艺界发展，可望告捷 。(凶带吉)',
		'39' => '云开见月，虽有忙碌，明亮坦途，指日可期 。(吉)',
		'40' => '一胜一衰，浮沉不定，功成身退，自获天佑 。(吉带凶)',
		'41' => '天赋吉运，德望兼备，无间努力，出息无穷 。(吉)',
		'42' => '事业不专，十九不成，专心进取，可望告捷 。(吉带凶)',
		'43' => '雨夜之花，外祥内苦，忍受自重，转凶为吉 。(吉带凶)',
		'44' => '虽专一计，事难遂愿，贪功好进，必招退步 。(凶)',
		'45' => '杨柳遇春，绿叶发枝，争执难关，一鸣惊人 。(吉)',
		'46' => '康庄小道，麻烦重重，若无耐性，难望有成 。(凶)',
		'47' => '有贵人助，可成大业，虽遇倒霉，浮沉不大 。(吉)',
		'48' => '丑化丰实，卓绝群伦，名利俱全，繁荣荣华 。(吉)',
		'49' => '遇吉则吉，遇凶则凶，惟靠介意，转危为安 。(凶)',
		'50' => '吉凶互见，一成一败，凶中有吉，吉中有凶 。(吉带凶)',
		'51' => '一盛一衰，浮沉不常，自重自处，可保平安 。(吉带凶)',
		'52' => '草木逢春，雨过天晴，渡过难关，即获告捷 。(吉)',
		'53' => '盛衰参半，外祥内苦，先吉后凶，先凶后吉 。(吉带凶)',
		'54' => '虽倾全力，难望告捷，此数大凶，逢极转运 。(凶)',
		'55' => '外观隆昌，内隐患难，制胜难关，开出泰运 。(吉带凶)',
		'56' => '适得其反，终难告捷，拔苗滋长，有头有尾 。(凶)',
		'57' => '努力规划，时来运转，野外枯草，春来花开 。(吉)',
		'58' => '半凶半吉，浮沉多端，始凶终吉，能保告捷 。(凶带吉)',
		'59' => '遇事犹疑，难望成事，大马金刀，始可有成 。(凶)',
		'60' => '阴晦无光，心迷意乱，朝三暮四，难定方针 。(凶)',
		'61' => '云遮半月，百隐风浪，应自介意，始保平安 。(吉带凶)',
		'62' => '烦闷懊丧，事事难展，自防灾祸，始免逆境 。方法。(凶)',
		'63' => '万物化育，繁荣之象，专心一意，始能告捷 。(吉)',
		'64' => '喜新厌旧，十九不成，白费无功，逢极转运 。(凶)',
		'65' => '吉运自来，能享盛名，驾驭时机，必获告捷 。(吉)',
		'66' => '白昼冗长，进退维谷，内外不合，信用短缺 。(凶)',
		'67' => '时来运转，事事如意，功成名就，荣华自来 。(吉)',
		'68' => '思虑周详，准备力行，不失先机，可望告捷 。(吉)',
		'69' => '摇荡不安，常陷逆境，不得时运，可贵成本 。(凶)',
		'70' => '惨淡规划，难免贫困，此数不吉，逢极转运 。(凶)',
		'71' => '吉凶参半，惟赖勇气，贯彻力行，始可告捷 。(吉带凶)',
		'72' => '利害混集，凶多吉少，得而复失，难以安顺 。(凶)',
		'73' => '安乐自来，天然吉利，力行不懈，终必告捷 。(吉)',
		'74' => '利不及费，坐食山空，如无智谋，难望告捷 。(凶)',
		'75' => '吉中带凶，拔苗滋长，进不如守，可保安详 。(吉带凶)',
		'76' => '此数大凶，破产之象，宜速改名，以避恶运 。(凶)',
		'77' => '先苦后甘，先甘后苦，如能守成，不致退步 。(吉带凶)',
		'78' => '有得有失，脆而不坚，须防劫财，始保平安 。(吉带凶)',
		'79' => '如走夜路，出息无光，希望不大，劳而无功 。(凶)',
		'80' => '得而复失，白劳神血，守成无贪，可保稳定 。(吉带凶)'
    ];

    /**
     * 测算吉凶。
     *
     * @param  string  $mobile  手机号。
     * @return array
     */
    public static function do($mobile)
    {
        if (Validator::is_mobilephone($mobile) === false) {
            YCore::exception(STATUS_SERVER_ERROR, '手机号不正确');
        }
        $num    = substr($mobile, -4);
        $code   = $num % 80;
        $result = self::$dict[$code];
        $tmp    = explode('。', $result);
        $Type   = self::type($tmp[1]);

        return [
            'mobile'       => $mobile,
            'tail_number'  => $num,     // 尾号。
            'lucky_number' => $code,    // 幸运码。
            'lucky_type'   => $Type,    // 1吉、2凶、3吉带凶
            'result'       => $result   // 测算结果。
        ];
    }

    /**
     * 吉凶后缀部分。
     *
     * @param  string  $resultSuffix  预测结果后半部带括号部分。
     * @return int
     */
    private static function type($resultSuffix)
    {
        if ($resultSuffix == '(吉)') {
            return self::TYPE_GOOD;
        } else if ($resultSuffix == '(凶)') {
            return self::TYPE_BAD;
        } else {
            return self::TYPE_BOTH;
        }
    }
}