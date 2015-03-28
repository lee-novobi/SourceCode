
//====Table Name====
#define TBL_DIVISION "division"
#define TBL_DIVISION_INFO_CHANGE "info_change_division"
#define TBL_DIVISION_INFO_CHANGE_HISTORY "info_change_division_history"
#define TBL_DIVISION_INVERTED_INDEX "inverted_index_division"
#define TBL_TMP_DIVISION "tmp_division"
#define TBL_DIVISION_HISTORY_LOG "division_log"

#define TBL_DEPARTMENT "department"
#define TBL_DEPARTMENT_INFO_CHANGE "info_change_department"
#define TBL_DEPARTMENT_INFO_CHANGE_HISTORY "info_change_department_history"
#define TBL_DEPARTMENT_INVERTED_INDEX "inverted_index_department"
#define TBL_TMP_DEPARTMENT "tmp_department"
#define TBL_DEPARTMENT_HISTORY_LOG "department_log"
#define TBL_DEPARTMENT_NOTI "notification_department"

#define TBL_PRODUCT "product"
#define TBL_PRODUCT_INFO_CHANGE "info_change_product"
#define TBL_PRODUCT_INFO_CHANGE_HISTORY "info_change_product_history"
#define TBL_PRODUCT_INVERTED_INDEX "inverted_index_product"
#define TBL_TMP_PRODUCT "tmp_product"
#define TBL_PRODUCT_HISTORY_LOG "product_log"
#define TBL_PRODUCT_NOTI "notification_product"

#define TBL_SERVER "server"
#define TBL_SERVER_INFO_CHANGE "info_change_server"
#define TBL_SERVER_INFO_CHANGE_HISTORY "info_change_server_history"
#define TBL_SERVER_INVERTED_INDEX "inverted_index_server"
#define TBL_TMP_SERVER_VIRTUAL  "tmp_server_vm"
#define TBL_TMP_SERVER_PHYSICAL "tmp_server_physical"
#define TBL_SERVER_HISTORY_LOG "server_log"
#define TBL_PHYSICAL_SERVER_NOTI "notification_server_physical"
#define TBL_VIRTUAL_SERVER_NOTI "notification_server_virtual"
#define TBL_TMP_PHYSICAL_SERVER_NOTI "tmp_notification_server_physical"

#define TBL_CI_CHANGES "ci_changes"
#define TBL_PARTNER_INFO "partner_info"
#define TBL_COLLECTOR_INFO "collector_info"
#define TBL_CI_RELATIONSHIP "ci_relationship"
#define TBL_USER "user"

//====Field Type====
#define BSON_STRING_TYPE 2
#define BSON_OBJECT_TYPE 3
#define BSON_ARRAY_TYPE 4
#define BSON_OBJECTID_TYPE 7

//===Config Define===
#define HOST "Host"
#define USER "User"
#define PASS "Password"
#define SRC "Source"
#define PORT "Port"
#define MONGODB_CMDB_GROUP "MONGODB_CMDB"
#define REPLICA_SET "ReplicaSet"
#define READ_REFERENCE "ReadReference"
#define READ_REFERENCE_SECONDARY "secondary"

//=== Config Synchronize ===
#define SYNC_FULL "full"
#define SYNC_CHANGE "change"

//=== Define action type ===
#define ACTION_INSERT 1
#define ACTION_UPDATE 2
#define ACTION_DELETE 3
#define ACTION_REINSERT 4
#define UNKNOWN -1
#define API_ACTION_SUCCESS 1
//=== ACTIVE Or INACTIVE
#define ACTIVE 1
#define INACTIVE 0
//=== MATCH Or NOTMATCH
#define MATCH 1
#define NOTMATCH 0
//=== DEFINE FUNCITON CHECKER ===
#define SERVER_STATISTIC "server_statistic"
#define HARDWARE_INFO "hardware_info"

//=== DEFINE SERVER TYPE ===
#define SERVER_VIRTUAL 1
#define SERVER_U 2
#define SERVER_CHASSIS 3
//=== DEFINE API NAME ===
#define API_GET_HARDWARE_BY_SERIALNUMBER "GetHardwareInfoBySerialNumber"
#define MSG_SOCKET_ERROR "Socket connection error"

