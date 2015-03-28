import sys, os
import sys, os
sys.path.append(os.path.join(os.path.dirname(os.path.abspath(__file__)), '../Utility'))

from inspect import stack
from Utility import Utilities

class CQuery:
    def __init__(self):
        pass

    def GetDivisionQuery(self):
        strSQL = '''
                SELECT * FROM CDM_iCMDB_DIVISION
                '''
        return strSQL

    def GetDepartmentQuery(self):
        strSQL = '''
                SELECT * FROM CDM_iCMDB_DEPARTMENT
                '''
        return strSQL

    def GetProductQuery(self):
        strSQL = '''
                SELECT * FROM CDM_iCMDB_TMP_PRODUCT_CODE
                '''
        return strSQL

    def GetPhysicalServerQuery(self):
        strSQL = '''
                SELECT * FROM CDM_iCMDB_SERVER
                '''
        return strSQL

    def GetVirtualServerQuery(self):
        strSQL = '''
                SELECT * FROM CDM_iCMDB_VM
                '''
        return strSQL

    def GetPhysicalServerIntefaceInfoQuery(self):
        strSQL = '''
                SELECT server_key, zbx_private_interfaces, zbx_public_interfaces
                FROM host_mdr
                WHERE integrity=1 AND zbx_server_type=1 AND is_deleted=0
                '''
        return strSQL

    def GetTechnicalOwnerInfoQuery(self):
        strSQL = '''
                SELECT q.NAME, q.EMAIL, q.DESCR, p.PRODUCT_CODE, p.CORE_PRODUCT
                FROM CDM_iCMDB_TECH_OWNER_DETAIL p LEFT JOIN CDM_iCMDB_TECH_OWNER q ON p.TECHNICAL_GROUP = q.NAME
                '''
        return strSQL

    def GetUserInfoQuery(self):
        strSQL = '''
                SELECT USERNAME, FULL_NAME, DEPARTMENT
                FROM CDM_iCMDB_USERS
                '''
        return strSQL