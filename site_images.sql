-- Create site_images table for managing website images
CREATE TABLE site_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_key VARCHAR(100) NOT NULL UNIQUE,
    image_path VARCHAR(255) NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default images
INSERT INTO site_images (image_key, image_path, title, description) VALUES
('logo', 'logo.png', 'Website Logo', 'The main logo of Kissan Agro Foods'),
('hero_bg', 'hero-bg.jpg', 'Hero Background', 'Background image for the hero section on the homepage'),
('about_image', 'about.jpg', 'About Image', 'Main image for the about page'),
('wheat_mill', 'wheat-mill.jpg', 'Wheat Mill', 'Image of our wheat flour mill'),
('rice_mill', 'rice-mill.jpg', 'Rice Mill', 'Image of our puffed rice mill'),
('team1', 'team1.jpg', 'CEO & Founder', 'Image of Rajesh Kumar, CEO & Founder'),
('team2', 'team2.jpg', 'Operations Manager', 'Image of Priya Sharma, Operations Manager'),
('team3', 'team3.jpg', 'Quality Control Manager', 'Image of Amit Patel, Quality Control Manager');
