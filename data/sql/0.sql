--
-- Database: `modules`
--

-- --------------------------------------------------------

--
-- Table structure for table `module`
--

CREATE TABLE IF NOT EXISTS `module` (
`module_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `url` varchar(500) NOT NULL,
  `meta_data` blob,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `photo_url` varchar(255) DEFAULT NULL,
  `owner` varchar(255) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `module_admin`
--

CREATE TABLE IF NOT EXISTS `module_admin` (
  `user_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
`user_id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `display_name` varchar(50) DEFAULT NULL,
  `password` varchar(128) NOT NULL,
  `photo_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `state` int(2) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_provider`
--

CREATE TABLE IF NOT EXISTS `user_provider` (
  `user_id` int(11) NOT NULL,
  `provider_id` varchar(50) NOT NULL,
  `provider` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `module`
--
ALTER TABLE `module`
 ADD PRIMARY KEY (`module_id`);

--
-- Indexes for table `module_admin`
--
ALTER TABLE `module_admin`
 ADD PRIMARY KEY (`user_id`,`module_id`), ADD KEY `module_id` (`module_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
 ADD PRIMARY KEY (`user_id`), ADD UNIQUE KEY `username` (`username`), ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_provider`
--
ALTER TABLE `user_provider`
 ADD PRIMARY KEY (`user_id`,`provider_id`), ADD UNIQUE KEY `provider_id` (`provider_id`,`provider`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `module_admin`
--
ALTER TABLE `module_admin`
ADD CONSTRAINT `module_admin_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `module` (`module_id`) ON DELETE CASCADE,
ADD CONSTRAINT `module_admin_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_provider`
--
ALTER TABLE `user_provider`
ADD CONSTRAINT `user_provider_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);
