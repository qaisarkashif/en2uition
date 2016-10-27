SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Table structure for table `converted_answer`
--
DROP TABLE IF EXISTS `converted_answer`;
CREATE TABLE IF NOT EXISTS `converted_answer` (
  `user_id` int(11) NOT NULL,
  `level_id` int(11) NOT NULL,
  `result` text NOT NULL,
  PRIMARY KEY (`user_id`,`level_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;