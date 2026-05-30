import DashboardController from './DashboardController'
import UserController from './UserController'
import RoleController from './RoleController'
import ItemController from './ItemController'
import EquipmentController from './EquipmentController'
import ToolController from './ToolController'
import LoanController from './LoanController'
import LoanReturnController from './LoanReturnController'
import RepositionController from './RepositionController'
import MaintenanceController from './MaintenanceController'
import MaintenanceCompanyController from './MaintenanceCompanyController'
import BorrowerController from './BorrowerController'
import SubjectController from './SubjectController'
import ReportController from './ReportController'
import Settings from './Settings'
const Controllers = {
    DashboardController: Object.assign(DashboardController, DashboardController),
UserController: Object.assign(UserController, UserController),
RoleController: Object.assign(RoleController, RoleController),
ItemController: Object.assign(ItemController, ItemController),
EquipmentController: Object.assign(EquipmentController, EquipmentController),
ToolController: Object.assign(ToolController, ToolController),
LoanController: Object.assign(LoanController, LoanController),
LoanReturnController: Object.assign(LoanReturnController, LoanReturnController),
RepositionController: Object.assign(RepositionController, RepositionController),
MaintenanceController: Object.assign(MaintenanceController, MaintenanceController),
MaintenanceCompanyController: Object.assign(MaintenanceCompanyController, MaintenanceCompanyController),
BorrowerController: Object.assign(BorrowerController, BorrowerController),
SubjectController: Object.assign(SubjectController, SubjectController),
ReportController: Object.assign(ReportController, ReportController),
Settings: Object.assign(Settings, Settings),
}

export default Controllers