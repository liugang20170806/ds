<?php
/**
 * @copyright Copyright(c) 2016 bravesoft.com.cn
 * @file menuUcenter.php
 * @brief 用户中心菜单管理
 * @author nswe
 * @date 2016/3/8 9:33:25
 * @version 4.4
 */
class menuUcenter
{
    //菜单的配制数据
	public static $menu = array(
		"交易记录" => array(
			"/ucenter/order" => "我的订单",
			"/ucenter/integral" => "我的积分",
			"/ucenter/redpacket" => "我的代金券",
		),

		"服务中心" => array(
			"/ucenter/refunds" => "退款申请",
			"/ucenter/complain" => "站点建议",
			"/ucenter/consult" => "商品咨询",
			"/ucenter/evaluation" => "商品评价",
		),

		"应用" => array(
			"/ucenter/message" => "短信息",
			"/ucenter/favorite" => "收藏夹",
		),

		"账户资金" => array(
			"/ucenter/account_log" => "帐户余额",
			"/ucenter/online_recharge" => "在线充值",
		),

		"个人设置" => array(
			"/ucenter/address" => "地址管理",
			"/ucenter/info" => "个人资料",
			"/ucenter/password" => "修改密码",
		),
	);

    /**
     * @brief 根据权限初始化菜单
     * @param int $roleId 角色ID
     * @return array 菜单数组
     */
    public static function init($roleId = "")
    {
		//菜单创建事件触发
		plugin::trigger("onUcenterMenuCreate");
		return self::$menu;
    }
}