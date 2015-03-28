#pragma once
#include "MongodbModel.h"
#include "../Common/Common.h"

class CNotificationModel: public CMongodbModel
{
public:
	CNotificationModel(void);
	~CNotificationModel(void);

	inline string GetLibraryName() { return m_strLibraryName; }
	inline void SetLibraryName(const string& strLibraryName) { m_strLibraryName = strLibraryName; }

	inline string GetAPIName() { return m_strAPIName; }
	inline void SetAPIName(const string& strAPIName) { m_strAPIName = strAPIName; }

	inline string GetPartnerName() { return m_strPartnerName; }
	inline void SetPartnerName(const string& strPartnerName) { m_strPartnerName = strPartnerName; }

	inline void SetData(const BSONObj& objData) { m_objBSON = objData; }
	inline bool IsNotified() { return m_bIsNotified; }
	inline void SetNotification(bool bIsNotified = true) { m_bIsNotified = bIsNotified; }

	virtual BSONObj GetNotificationInfo(BSONObj boCIInfo, BSONObj boNewCIInfo){};
protected:
	//virtual void InitLookUpAPIField(){};
	//map<string, string> m_mapLookUpAPIField;
	string m_strLibraryName;
	string m_strAPIName;
	string m_strPartnerName;
	bool m_bIsNotified;
	//bool m_bIsFieldMapping;
};
