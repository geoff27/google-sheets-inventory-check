function discoverQty(mfr, partnum, qty_ordered, location)
	{		
	 $("#qtyinfodiv_"+location).html('Checking...');																																																							
	   $.post('inventory_check.php', {mfr:mfr, partnum:partnum, qty_ordered:qty_ordered}, function(data){																					    
			 $("#qtyinfodiv_"+location).html(data);   // update the little space next to the partnum with either the Qty or ???
		});		
	}	
