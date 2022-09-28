--
-- Table structure for table `patient_devices_list`
--

CREATE TABLE IF NOT EXISTS `patient_devices_list` (
    `id` int NOT NULL COMMENT 'Primary Key',
    `pid` bigint DEFAULT NULL,
    `subehremrid` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `deviceid` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `devicemodal` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `devicemaker` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `deviceos` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='comlink';

--
-- Table structure for table `patient_monitoring_form`
--

CREATE TABLE IF NOT EXISTS `patient_monitoring_form` (
   `id` int NOT NULL COMMENT 'Primary Key',
   `pid` bigint NOT NULL COMMENT 'Patient ID',
   `pm_id` int NOT NULL COMMENT 'patient monitoring ID',
   `facility` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
   `provider` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
   `weight` float(5,2) DEFAULT NULL,
  `height` float(5,2) DEFAULT NULL,
  `bp_upper` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bp_lower` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `temp_upper` float(5,2) DEFAULT NULL,
  `temp_lower` float(5,2) DEFAULT NULL,
  `bs_upper` smallint DEFAULT NULL,
  `bs_lower` smallint DEFAULT NULL,
  `resp_upper` float(5,2) DEFAULT NULL,
  `resp_lower` float(5,2) DEFAULT NULL,
  `oxy_upper` float(5,2) DEFAULT NULL,
  `oxy_lower` float(5,2) DEFAULT NULL,
  `pain_upper` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pain_lower` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `alert` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `updatedAt` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='comlink';


--
-- Table structure for table `patient_monitoring_list`
--

CREATE TABLE IF NOT EXISTS `patient_monitoring_list` (
   `id` int NOT NULL COMMENT 'Primary Key',
   `pid` bigint NOT NULL COMMENT 'Patient ID',
   `updatedAt` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='patient monitoring';


--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `patient_devices_list`
--
ALTER TABLE `patient_devices_list`
    MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `patient_monitoring_form`
--
ALTER TABLE `patient_monitoring_form`
    MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'Primary Key', AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `patient_monitoring_list`
--
ALTER TABLE `patient_monitoring_list`
    MODIFY `id` int NOT NULL AUTO_INCREMENT COMMENT 'Primary Key';
COMMIT;
