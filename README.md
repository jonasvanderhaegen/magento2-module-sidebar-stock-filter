# Sidebar Stock Filter Module

This Magento 2 module introduces a custom product attribute (`custom_filter_qty`) to enhance the filtering options in layered navigation (sidebar). It allows for filtering products based on custom stock quantities, improving user experience and product discovery.

## Module Purpose

The primary goal of this module is to:
- Enable filtering by a custom stock quantity (`custom_filter_qty`) in the layered navigation.
- Provide flexibility to configure and manage the attribute in the admin panel.
- Support seamless integration with product and category filtering mechanisms.

In this case, at the company Odoo was updating the quantity for majority if the products, when it updates qty through api call it also updates 'custom_filter_qty'. Because the attribute is type "price" as a visitor you can then use the range slider in the navigation sidebar to select range for quantity.

## Features

1. **Custom Product Attribute**  
   - Adds the `custom_filter_qty` attribute to the catalog product entity.  
   - Supports decimal values for fractional quantities if needed.  
   - Includes backend validation for numeric input (`validate-number`).  

2. **Layered Navigation Integration**  
   - Dynamically removes the custom filter from layered navigation when the module is disabled.  
   - Fully integrates with Magento's native category and catalog search filtering.

3. **Configuration Management**  
   - Respects the module's enable/disable configuration at the website level.  
   - Can be reverted cleanly using Magento's data patch mechanisms.

4. **Revertable Data Patch**  
   - Adds or removes the `custom_filter_qty` attribute as needed during deployment or rollback.  

## Repository Structure

The module is structured to align with Magento 2's best practices. Typically, this module resides under `app/code/Jvdh/SidebarStockFilter`.

### Included Components

1. **Setup Data Patch**  
   - `Setup/Patch/Data/AddCustomFilterQtyProductAttribute.php`: Creates and configures the `custom_filter_qty` attribute for products.

2. **Helper Class**  
   - `Helper/Data.php`: Provides reusable methods to check module enablement and define attribute constants.

3. **Observers**  
   - `Observer/UpdateFilterStockValue.php`: Updates the `custom_filter_qty` attribute based on stock item changes.  
   - `Observer/UpdateFilterStockValueByOrder.php`: Adjusts the `custom_filter_qty` attribute when an order is placed.

4. **Plugins**  
   - `Plugin/Catalog/Model/Layer/FilterList.php`: Dynamically modifies the layered navigation filters to include or exclude the custom filter.

## Configuration

1. **Admin Settings**  
   - Navigate to `Stores > Configuration > Sidebar Stock Filter > General`.  
   - Enable or disable the module at the website level.

2. **Custom Attribute**  
   - The `custom_filter_qty` attribute is added under the **General** group in product attributes.  
   - The attribute can be edited in the admin panel for individual products.

3. **Frontend Visibility**  
   - By default, the attribute is not visible on the frontend but is available for filtering in layered navigation.

## Notes

- The module integrates seamlessly with Magento's layered navigation, ensuring no disruptions to existing functionality.
- Proper testing is recommended when deploying the module in production environments with large catalogs.
- The `custom_filter_qty` attribute supports decimal values, which can be useful for specific business use cases.

## License

This module is licensed under the MIT License. Feel free to use, modify, and distribute it as needed.

## Author

Developed by Jvdh.

## Contact

For any questions, issues, or feature requests, please feel free to reach out via [your email or LinkedIn profile].