#include "DivisionModel.h"

CDivisionModel::CDivisionModel(void)
{
	m_strName = "";
	m_strCode = "";
	m_strAlias = "";
	m_strHRId = "";
	m_strHRCode = "";
	
	m_iStatus = -1;
	m_iDeleted = 0;	
}

CDivisionModel::~CDivisionModel(void)
{
}

BSONObj CDivisionModel::GetDivisionInfo()
{
	BSONObj boDataInfo = BSON("name" << m_strName << "code" << m_strCode << "alias" << m_strAlias <<
							  "hr_id" << m_strHRId << "hr_code" << m_strHRCode <<
							  "status" << m_iStatus << "deleted" << m_iDeleted );
	return boDataInfo;
}