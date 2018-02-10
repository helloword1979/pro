package main

import (
	"encoding/json"
	"log"
	"fmt"
)

func main() {
	x1 := new(AutoGenerated)
	pc := X1{}
	t1 := make(map[string]X1)
	t2 := make(map[string]X2)
	x1.T1 = t1
	x1.T2 = t2
	pc.ID = "1"
	pc.Mtype = "1"
	pc.Ptype = "pc"
	pc.FcType = ""
	pc.Starttime = "1516838400"
	pc.Endtime = "1517097600"
	pc.Remark = ""
	pc.Addtime = "1513601840"
	pc.Updatetime = "1516846361"
	pc.Startdate = "2018-01-25 00:00:00"
	pc.Enddate = "2018-01-28 00:00:00"
	pc.LineID = []string{"aaa", "vxd", "zm"}
	x1.T1["Pc"] = pc
	agent := X1{}
	agent.ID = "1"
	agent.Mtype = "1"
	agent.Ptype = "pc"
	pc.LineID = []string{"aaa", "xgg", "vvly"}
	agent.FcType = ""
	agent.Starttime = "1516838400"
	agent.Endtime = "1517097600"
	agent.Remark = ""
	agent.Addtime = "1513601840"
	agent.Updatetime = "1516846361"
	agent.Startdate = "2018-01-25 00:00:00"
	agent.Enddate = "2018-01-28 00:00:00"
	x1.T1["agent"] = agent
	//
	pc2 := X2{}
	pc2.FcType = make(map[string][]string)
	pc2.lineList = make(map[string]X3)
	pc2.Mtype = "2"
	pc2.Ptype = "pc"
	pc2.LineID = []string{"aaa", "mlgklu", "aqcrt"}
	fcTypeTemp := []string{"fc_3d", "pl_3"}
	pc2.FcType["aaa"] = fcTypeTemp
	fcTypeTemp2 := []string{"jsliuhecai"}
	pc2.FcType["aaa"] = fcTypeTemp2
	fcTypeTemp3 := []string{"ffc_o", "jsfc", "liuhecai"}
	pc2.FcType["aaa"] = fcTypeTemp3
	pc2.Starttime = "1516838400"
	pc2.Endtime = "1517011200"
	pc2.Remark = ""
	pc2.Addtime = "1513602124"
	pc2.Updatetime = "1516864359"
	pc2.Startdate = "2018-01-25 00:00:00"
	pc2.Enddate = "2018-01-27 00:00:00"
	lineList := X3{}
	lineList.ID = "6"
	lineList.Mtype = "2"
	lineList.Ptype = "pc"
	lineList.LineID = "aaa"
	lineList.Starttime = "1516838400"
	lineList.Endtime = "1517011200"
	lineList.Remark = ""
	lineList.Addtime = "1513602124"
	lineList.Updatetime = "1516864359"
	lineList.Startdate = "2018-01-25 00:00:00"
	lineList.Enddate = "2018-01-27 00:00:00"
	lineList.FcType = []string{"fc_3d", "pl_3"}
	pc2.lineList["aaa"] = lineList
	mlglu := X3{}
	mlglu.ID = "14"
	mlglu.Mtype = "2"
	mlglu.Ptype = "pc"
	mlglu.LineID = "mlgklu"
	mlglu.FcType = []string{"jsliuhecai"}
	mlglu.Starttime = "1516838400"
	mlglu.Endtime = "1517011200"
	mlglu.Remark = ""
	mlglu.Addtime = "1516863596"
	mlglu.Updatetime = "1516864359"
	mlglu.Startdate = "2018-01-25 00:00:00"
	mlglu.Enddate = "2018-01-27 00:00:00"
	pc2.lineList["mlgklu"] = mlglu
	wap := *&pc2
	x1.T2["pc"] = pc2
	x1.T2["wap"] = wap

	bytes, err := json.MarshalIndent(&x1, "   ", " ")
	if err != nil {
		log.Fatal("err")
		return
	}
	fmt.Println(string(bytes))

}

type X1 struct {
	ID         string   `json:"id"`
	Mtype      string   `json:"mtype"`
	Ptype      string   `json:"ptype"`
	LineID     []string `json:"line_id"`
	FcType     string   `json:"fc_type"`
	Starttime  string   `json:"starttime"`
	Endtime    string   `json:"endtime"`
	Remark     string   `json:"remark"`
	Addtime    string   `json:"addtime"`
	Updatetime string   `json:"updatetime"`
	Startdate  string   `json:"startdate"`
	Enddate    string   `json:"enddate"`
}
type X2 struct {
	Mtype      string              `json:"mtype"`
	Ptype      string              `json:"ptype"`
	LineID     []string            `json:"line_id"`
	FcType     map[string][]string `json:"fc_type"`
	Starttime  string              `json:"starttime"`
	Endtime    string              `json:"endtime"`
	Remark     string              `json:"remark"`
	Addtime    string              `json:"addtime"`
	Updatetime string              `json:"updatetime"`
	Startdate  string              `json:"startdate"`
	Enddate    string              `json:"enddate"`
	lineList   map[string]X3       `json:"x_3"`
}
type X3 struct {
	ID         string   `json:"id"`
	Mtype      string   `json:"mtype"`
	Ptype      string   `json:"ptype"`
	LineID     string   `json:"line_id"`
	FcType     []string `json:"fc_type"`
	Starttime  string   `json:"starttime"`
	Endtime    string   `json:"endtime"`
	Remark     string   `json:"remark"`
	Addtime    string   `json:"addtime"`
	Updatetime string   `json:"updatetime"`
	Startdate  string   `json:"startdate"`
	Enddate    string   `json:"enddate"`
}

type AutoGenerated struct {
	T1 map[string]X1
	T2 map[string]X2
}