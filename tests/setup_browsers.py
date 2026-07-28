import selenium.webdriver as webdriver
import os
# Initialize a Firefox instance
fxoptions = webdriver.firefox.options.Options()
fxoptions.profile.accept_untrusted_certs = True
firefox = webdriver.Firefox(fxoptions)

# Initialize a Thorium instance
thorium_options = webdriver.ChromeOptions()
if os.name=="nt":
    username = os.getlogin()
    thorium_path = os.path.join("C:", "Users", username, "AppData", "Local", "Thorium", "Application")

    thorium_options.binary_location = os.path.join(thorium_path, "thorium.exe")
elif os.name=="posix":
    thorium_path = "/usr/bin/thorium-browser"
    thorium_options.binary_location = thorium_path
    
thorium = webdriver.Chrome(thorium_options)

# Initialize an Epiphany instance
epiphany_options = webdriver.WebKitGTKOptions()
epiphany = webdriver.WebKitGTK(epiphany_options)

selected_element_firefox = None
selected_element_thorium = None
selected_element_epiphany = None

# Add functions to move all browsers
def navigate_to(url: str):
    firefox.get(url)
    thorium.get(url)
    epiphany.get(url)
