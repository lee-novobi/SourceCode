#include "DCProductNotificationAPI.h"
#include "../Model/NotificationModel.h"

CDCProductNotificationAPI::CDCProductNotificationAPI(void)
{
}

CDCProductNotificationAPI::~CDCProductNotificationAPI(void)
{
}

string CDCProductNotificationAPI::Convert2JSON(CNotificationModel* pData)
{
	BSONObj boResult;
	BSONObj boTemp = *pData;
	boResult = BSON(
					"ProductCode"		<< boTemp["code"] <<
					"ProductName"		<< boTemp["alias"] <<
					"ProductAlias"		<< boTemp["alias"] <<
					"ProductDepartment" << boTemp["department_alias"] <<
					"ProductDivision"	<< boTemp["division_alias"]
		);
	return boResult.jsonString();
}