#include "NotificationAPI.h"
#include "../Model/NotificationModel.h"
#include "../Common/DBCommon.h"
#include <dlfcn.h>

CNotificationAPI::CNotificationAPI(void)
{
}

CNotificationAPI::~CNotificationAPI(void)
{
}

bool CNotificationAPI::Notify(CNotificationModel* pData)
{
	bool bResult = false;
	int iResponse;
	BSONObj boData;
	stringstream strErrorMess;
	string strLog, strData;
	int iActionType;
	strData = Convert2JSON(pData);
	boData = *pData;
	// open the library
	void* handle = dlopen((const char*)pData->GetLibraryName().c_str(), RTLD_LAZY);
	if (!handle) 
	{		
		strErrorMess << "Cannot open library: " << dlerror() << __FILE__ << "|" << __LINE__;		
		strLog = CUtilities::FormatLog(ERROR_MSG, "CNotificationAPI", "Notify","error:" + strErrorMess.str());
		CUtilities::WriteErrorLog(ERROR_MSG, strLog);

		return false;
	}

	// reset errors
	dlerror();
	NotifyInfoChange apiNotifyInfo = (NotifyInfoChange)dlsym(handle, (const char*)pData->GetAPIName().c_str());
	const char *dlsym_error = dlerror();
	if (dlsym_error) {
		strErrorMess << "Cannot load symbol " << pData->GetAPIName().c_str() << ":" << dlsym_error;
		strLog = CUtilities::FormatLog(ERROR_MSG, "CNotificationAPI", "Notify","error:" + strErrorMess.str());
		CUtilities::WriteErrorLog(ERROR_MSG, strLog);

		// close the library
		dlclose(handle);
	}

	iActionType = boData.getIntField("action_type");
	if(iActionType != ACTION_DELETE){
		iResponse = apiNotifyInfo(strData.c_str(), iActionType);
	}
	else{
		iResponse = apiNotifyInfo(boData.getStringField("code"), iActionType);
	}

	if(iResponse == API_ACTION_SUCCESS){
		bResult = true;
	}
	// close the library
	dlclose(handle);

	return bResult;
}
