import menu from "./id/components/menu.json";
import buttons from "./id/components/buttons.json";
import alert_placeholder from "./id/components/alert-placeholder.json";
import dropdown from "./id/components/dropdown.json";
import profile_menu from "./id/components/profile-menu.json";
import language_switcher from "./id/components/language-switcher.json";
import search_box from "./id/components/search-box.json";
import sidebar_pop from "./id/components/sidebar-pop.json";
import data_list from "./id/components/data-list.json";
import user_location from "./id/components/user-location.json";
import delete_modal from "./id/components/delete-modal.json";
import file_upload from "./id/components/file-upload.json";

import login from "./id/views/login.json";
import register from "./id/views/register.json";
import forgot_password from "./en/views/forgot_password.json";
import reset_password from "./en/views/reset_password.json";
import profile from "./id/views/profile.json";
import user from "./id/views/user.json";
import company from "./id/views/company.json"
import branch from "./id/views/branch.json"
import warehouse from "./id/views/warehouse.json"
import error from "./id/views/error.json"
import customer_group from "./id/views/customer_group.json"

export default {
    "components": {
        "menu": menu,
        "alert-placeholder": alert_placeholder,
        "buttons": buttons,
        "dropdown": dropdown,
        "data-list": data_list,
        "user-location": user_location,
        "delete-modal": delete_modal,
        "file-upload": file_upload,
        "profile-menu": profile_menu,
        "language-switcher": language_switcher,
        "search-box": search_box,
        "sidebar-pop": sidebar_pop
    },
    "views": {
        "login": login,
        "register": register,
        "forgot_password": forgot_password,
        "reset_password": reset_password,
        "profile": profile,
        "user": user,
        "company": company,
        "branch": branch,
        "warehouse": warehouse,
        "error": error,
        "customer_group": customer_group,
    }
}