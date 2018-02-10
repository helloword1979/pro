package schema

import "global"

//会员退水设定
type MemberRetreatWaterSet struct {
	Id          int64  `xorm:"id PK autoincr"`
	SiteId      string `xorm:"site_id"`       //操作站点id
	SiteIndexId string `xorm:"site_index_id"` //站点前台id
	ValidMoney  int64  `xorm:"valid_money"`   //有效总投注
	DiscountUp  int64  `xorm:"discount_up"`   //优惠上限
	DeleteTime  int64  `xorm:"delete_time"`   //删除时间
}

func (*MemberRetreatWaterSet) TableName() string {
	return global.TablePrefix + "member_retreat_water_set"
}
