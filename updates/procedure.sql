
-- cleanupCmsData procedure
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`max`@`localhost` PROCEDURE `cleanupCmsData`()
    MODIFIES SQL DATA
    SQL SECURITY INVOKER
BEGIN
    DECLARE v_delete_limit INT DEFAULT 1000;
    DECLARE v_row_count INT DEFAULT 0;
    
    SELECT 'Cleaning CMS User Auth...';
    REPEAT
        -- SELECT '.';
        START TRANSACTION;
        DELETE FROM tm_user_auth
            WHERE `timestamp` < DATE_SUB( NOW(), INTERVAL 1 MONTH )
            ORDER BY `id`
            LIMIT v_delete_limit;
        SET v_row_count = ROW_COUNT();
        COMMIT;
        
        SELECT CONCAT( '... deleted ', v_row_count );
    UNTIL v_row_count < v_delete_limit
    END REPEAT;
END$$

DELIMITER ;

-- --------------------------------------------------------