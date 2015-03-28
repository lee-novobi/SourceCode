#include "DepartmentInfoChangeModel.h"

CDepartmentInfoChangeModel::CDepartmentInfoChangeModel(void)
{
	InitLookUpFieldValue();
}

CDepartmentInfoChangeModel::~CDepartmentInfoChangeModel(void)
{
}

void CDepartmentInfoChangeModel::InitLookUpFieldValue()
{
	m_bIsValueMapping = true;
	m_mapLookUpFieldValue["status"]["0"] = "Inactive";
	m_mapLookUpFieldValue["status"]["1"] = "Active";	
}