SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


INSERT INTO `workattribute` (`id`, `field`, `type`) VALUES
(1, 'Series', 'Select'),
(2, 'Part', 'Text'),
(3, 'Volume', 'Text'),
(4, 'Pages', 'Text'),
(5, 'Language', 'Select'),
(6, 'Notes', 'Textarea'),
(7, 'Periodical', 'Select'),
(8, 'Edition', 'Text'),
(10, 'Number', 'Text'),
(11, 'ISSN', 'Text'),
(12, 'Total Pages', 'Text'),
(13, 'Material Designation', 'Select'),
(14, 'ISBN', 'Text'),
(20, 't_ShortText', 'Text'),
(21, 't_LongText', 'Textarea'),
(22, 't_TrueFalseBox', 'RadioButton'),
(23, 't1_OptionDropDown', 'Select');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
