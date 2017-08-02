SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


INSERT INTO `folder` (`id`, `parent_id`, `text_en`, `text_fr`, `text_de`, `text_nl`, `text_es`, `text_it`, `number`, `sort_order`) VALUES
(1, NULL, 'Biography', 'Biographie', 'Lebensbeschreibung', 'Biografie', 'Biografía', 'Biografia', '01', 200),
(3035, NULL, 'Works', 'Oeuvres', 'Werke', 'Werken', 'Obras', 'Opere', '02', 300),
(10434, NULL, 'Doctrine', 'Doctrine', 'Doktrin', 'Doctrine', 'Doctrina', 'Dottrina', '03', 400),
(16938, NULL, 'Influence and Survival', 'Influence et Survie', 'Einfluss und Überlebung', 'Invloed en Overleving', 'Influencia y Supervivencia', 'Influenza e Sopravvivenza', '04', 500);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
