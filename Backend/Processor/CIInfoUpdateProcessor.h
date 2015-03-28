#pragma once
#include "Processor.h"

class CConfigFile;
class CCMDBController;
class CCIInfoUpdateController;
class CCIInfoChangeController;
class CCIHistoryLogController;
class CNotificationController;
class CPartnerInfoController;
class CCIRelationshipController;
class CCIInfoChangeModel;
class CCIHistoryLogModel;
class CNotificationModel;
class CMongodbModel;

struct CIRelation
{
	string strRelatedCI;
	string strTmpCICollection;
	string strRelatedField;
};
typedef map<string,MongodbModelArray*> mapKey2BSONObjArrayPtr;
typedef map<string,CMongodbModel*> mapKey2BSONObjPtr;
typedef map<string,vector<CIRelation> > mapField2BSONElementArray;


class CCIInfoUpdateProcessor: public CProcessor
{
public:
	CCIInfoUpdateProcessor(const string& strFileName);
	~CCIInfoUpdateProcessor(void);

	bool ProceedUpdateCIInfo();

protected:
	auto_ptr<DBClientCursor> LoadNewCIInfo();
	auto_ptr<DBClientCursor> LoadCIRelationship();
	void PrepareUpdating(auto_ptr<DBClientCursor> &ptrInfoUpdateCursor);
	void PrepareCIRelationshipInfo(auto_ptr<DBClientCursor> &ptrCIRelationshipCursor);
	void GetCurrentCIInfo(auto_ptr<DBClientCursor> &ptrCIInfoCursor);
	void ETLNotification(auto_ptr<DBClientCursor> &ptrCIInfoCursor);
	bool LaunchUpdateProcess();
	bool TrackChangedCIInfo(BSONElement beObjID, BSONObj boOldCIInfo, 
							BSONObj boNewCIInfo, BSONObjArray& vTrackChangedCursor);
	bool InsertHistoryLog(BSONElement beObjID, BSONObj boOldCIInfo, BSONObj boNewCIInfo);
	bool UpdateCIInfo(string strKey, BSONObj bsCIInfo);
	bool DeleteTmpCIInfo(BSONElement beId);

	auto_ptr<DBClientCursor> GetRelatedCIInfo(string strTable, BSONObj boCondition);

	BSONObj RemoveRedundantFields(BSONObj pCIInfo);
		
	virtual bool Connect();
	virtual bool CreateNotificationData(int iActionType, BSONObj boNotificationData,
		BSONObj boChangedFields, BSONObjArray arrPartnerInfo);

	virtual void ProcessUpdateRelatedCI(BSONObj boOldCIInfo, BSONObj boChangedData,
		BSONObj boChangedFields);

	void DestroyMapData();

protected:
	bool m_bIsNotification;
	int m_iCIType;

	mapKey2BSONObjArrayPtr m_mapKey2NewCIInfoArray;
	mapKey2BSONObjPtr m_mapKey2CurrCIInfo;
	mapField2BSONElementArray m_mapField2CIRelationshipArray;

	CCMDBController *m_pCMDBController;
	CCIInfoChangeController *m_pCIInfoChangeController;
	CCIInfoUpdateController *m_pCIInfoUpdateController;
	CCIHistoryLogController *m_pCIHistoryLogController;
	CNotificationController *m_pNotificationController;
	CPartnerInfoController *m_pPartnerInfoController;
	CCIRelationshipController *m_pCIRelationshipController;
	CCIInfoChangeModel *m_pCIInfoChangeModel;	
	CCIHistoryLogModel *m_pCIHistoryLogModel;
	CNotificationModel *m_pNotificationModel;
	
};