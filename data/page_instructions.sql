SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


INSERT INTO `page_instructions` (`id`, `page_name`, `instructions`) VALUES
(1, 'public/', 'PRODUCTION - Live since 21st, November 2005\n\n                                 Running on NEW server July 2007'),
(2, 'Work/new', 'Never use double quotes in the title, instead use single quotes when needed.'),
(3, 'Work/search', 'test'),
(4, 'Work/manage', 'Create a new card (use the lookup function to retrieve basic information based on the title) or select a title to review, change or publish an existing card.'),
(5, 'Work/manage?action=review', ''),
(6, 'Work/manage?action=classify', ''),
(7, 'WorkType/new?action=new', ''),
(8, 'WorkType/manage', ''),
(9, 'WorkType/attributes', ''),
(10, 'Classification/new?action=new', ''),
(11, 'Classification/manage', ''),
(12, 'Classification/merge?action=merge_classification', ''),
(13, 'exportlistclassification', ''),
(14, 'Agent/new?action=new', ''),
(15, 'Agent/find', ''),
(16, 'Agent/manage', ''),
(17, 'Agent/merge?action=merge', ''),
(18, 'AgentType/new?action=new', ''),
(19, 'AgentType/manage', ''),
(20, 'Publisher/newpublisher?action=new', ''),
(21, 'Publisher/findpublisher', ''),
(22, 'Publisher/managepublisher', ''),
(23, 'Publisher/merge', ''),
(24, 'Language/new?action=new', ''),
(25, 'Language/manage', 'The english words cannot be changed.'),
(26, 'Users/new?action=new', ''),
(27, 'Users/manage', ''),
(28, 'Users/access', ''),
(29, 'Preferences/changepassword?action=change_pwd', 'Enter here your new password (second time for confirmation).'),
(30, 'WorkType/manage_attribute_options', 'test');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
