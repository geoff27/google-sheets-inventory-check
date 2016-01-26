# google-sheets-inventory-check
A clunky but effective way to maintain a quasi-live store inventory in a Google sheet, and access quantity information (based on part #) in PHP/Javascript.

Essentially, imagine you run an ecommerce site, and you have a Google Sheets inventory spreadsheet with a separate tab/worksheet for each manufacturer of products that you carry (for example, Nike, Reebok, Adidas, etc). Each worksheet has at least two columns, one with unique part numbers or UPCs for each item, and one with the corresponding Quantity in stock. You want to be able to see, without leaving a customer's order, how many you have in stock of each item they ordered.

This combination of PHP and Javascript will let you drop an onClick link anywhere in your web app (for instance, on a customer's order) that, when clicked, returns the current quantity in stock of that particular item, based on the unique part number found in the Google sheet. To (possibly) save a little time and/or effort, the first time the script is run, it stores the contents of that particular worksheet in a $_SESSION variable, and for the next ten minutes that the onClick is called for an item in that worksheet, it will get the value from the $_SESSION variable instead of querying the Google Sheet again each time.

Additionally, when the onClick is clicked, it sends the quantity of that item that was ordered, and if the corresponding quantity in the Google sheet is greater than or equal to the quantity ordered, the value returned will be green, and if it's less, it will be in red, to quickly indicate to the user whether or not there are enough of that item in inventory to fill the order.

See the usage_example.html file for an easy way to implement this.
