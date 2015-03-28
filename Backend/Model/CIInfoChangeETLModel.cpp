#include "CIInfoChangeETLModel.h"
#include "../Common/DBCommon.h"

CCIInfoChangeETLModel::CCIInfoChangeETLModel(void)
{
}

CCIInfoChangeETLModel::~CCIInfoChangeETLModel(void)
{
}

BSONObj CCIInfoChangeETLModel::GetUpdateRecord(BSONObj boCIChangeRecord)
{
	BSONObj boResult, boNew;
	try
	{
		boNew = boCIChangeRecord["new"].Obj();
		boResult = BSON("old_src"<<OLD_FIELD<<
			            "old"<<boCIChangeRecord["old"]<<
						"ci_id"<<boCIChangeRecord["ci_id"]<<
						"clock"<<boCIChangeRecord["clock"]<<
						"change_by"<<boCIChangeRecord["change_by"]<<
						"action_type"<<ACTION_TYPE_UPDATE<<
						"deleted"<<0);
		boResult = CMongodbModel::MergeBSONObj(&boResult,&boNew);
	}
	catch(exception& ex)
	{	
		stringstream strErrorMess;
		string strLog;
		strErrorMess << ex.what() << "][" << __FILE__ << "|" << __LINE__ ;
		strLog = CUtilities::FormatLog(ERROR_MSG, "CCIInfoChangeETLModel", "GetUpdateRecord(BSONObj)","exception:" + strErrorMess.str());
		CUtilities::WriteErrorLog(strLog);
	}
	return boResult;
}