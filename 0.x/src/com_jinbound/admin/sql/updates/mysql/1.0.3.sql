########################################
##    Lead IP Address                 ##
##                                    ##
########################################;

ALTER TABLE #__jinbound_leads ADD COLUMN `ip` VARCHAR(255) NOT NULL DEFAULT '' AFTER last_name;
