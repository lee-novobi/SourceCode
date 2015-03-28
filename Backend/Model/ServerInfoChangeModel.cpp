#include "ServerInfoChangeModel.h"

CServerInfoChangeModel::CServerInfoChangeModel(void)
{
	InitLookUpFieldValue();
}

CServerInfoChangeModel::~CServerInfoChangeModel(void)
{
}

void CServerInfoChangeModel::InitLookUpFieldValue()
{
	m_bIsValueMapping = true;
	m_mapLookUpFieldValue["status"]["0"] = "Unused";
	m_mapLookUpFieldValue["status"]["1"] = "In Used";
	m_mapLookUpFieldValue["status"]["2"] = "Borrow";
	m_mapLookUpFieldValue["status"]["3"] = "Transfer";
	m_mapLookUpFieldValue["status"]["4"] = "Error";
	m_mapLookUpFieldValue["power_status"]["0"] = "Off";
	m_mapLookUpFieldValue["power_status"]["1"] = "On";
	m_mapLookUpFieldValue["power_status"]["2"] = "Unknown";
	m_mapLookUpFieldValue["server_type"]["-1"] = "Unknown";
	m_mapLookUpFieldValue["server_type"]["1"] = "Virtual";
	m_mapLookUpFieldValue["server_type"]["2"] = "Server U";
	m_mapLookUpFieldValue["server_type"]["3"] = "Server Chassis";
}