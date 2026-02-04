<?php
/**
 * Menu Update Migration - February 2026
 * Updates the restaurant menu from PDF and adds phone numbers to site settings
 */

// Database connection - using actual production credentials
$host = 'promanaged-it.com';
$dbname = 'p601229_hotels';
$username = 'p601229_hotel_admin';
$password = '2:p2WpmX[0YTs7';

$pdo = null;

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Starting menu update...\n";
    echo "Connected to database successfully.\n\n";
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Step 1: Delete existing food menu items
    echo "Step 1: Clearing existing food menu items...\n";
    $stmt = $pdo->prepare("DELETE FROM food_menu");
    $stmt->execute();
    $deletedItems = $stmt->rowCount();
    echo "Deleted $deletedItems existing menu items\n";
    
    // Step 2: Delete existing menu categories
    echo "Step 2: Clearing existing menu categories...\n";
    $stmt = $pdo->prepare("DELETE FROM menu_categories");
    $stmt->execute();
    $deletedCats = $stmt->rowCount();
    echo "Deleted $deletedCats existing categories\n";
    
    // Step 3: Insert new categories
    echo "Step 3: Creating new menu categories...\n";
    $categories = [
        ['name' => 'Breakfast', 'slug' => 'breakfast', 'description' => 'Start your day with our delicious breakfast options', 'order' => 1],
        ['name' => 'Starter', 'slug' => 'starter', 'description' => 'Appetizing starters to begin your meal', 'order' => 2],
        ['name' => 'Chicken Corner', 'slug' => 'chicken-corner', 'description' => 'Delicious chicken dishes prepared to perfection', 'order' => 3],
        ['name' => 'Meat Corner', 'slug' => 'meat-corner', 'description' => 'Premium meat dishes for carnivores', 'order' => 4],
        ['name' => 'Fish Corner', 'slug' => 'fish-corner', 'description' => 'Fresh fish and seafood from Lake Malawi', 'order' => 5],
        ['name' => 'Pasta Corner', 'slug' => 'pasta-corner', 'description' => 'Italian pasta classics and favorites', 'order' => 6],
        ['name' => 'Burger Corner', 'slug' => 'burger-corner', 'description' => 'Juicy burgers made with premium Malawian beef', 'order' => 7],
        ['name' => 'Pizza Corner', 'slug' => 'pizza-corner', 'description' => 'Authentic pizzas with various toppings', 'order' => 8],
        ['name' => 'Snack Corner', 'slug' => 'snack-corner', 'description' => 'Quick bites and light snacks', 'order' => 9],
        ['name' => 'Indian Corner', 'slug' => 'indian-corner', 'description' => 'Authentic Indian cuisine with aromatic spices', 'order' => 10],
        ['name' => 'Liwonde Sun Specialities', 'slug' => 'liwonde-sun-specialities', 'description' => 'Our signature special dishes', 'order' => 11],
        ['name' => 'Extras', 'slug' => 'extras', 'description' => 'Additional sides and extras', 'order' => 12],
        ['name' => 'Desserts', 'slug' => 'desserts', 'description' => 'Sweet treats to end your meal', 'order' => 13]
    ];
    
    $categoryIds = [];
    foreach ($categories as $cat) {
        $stmt = $pdo->prepare("INSERT INTO menu_categories (name, slug, description, display_order, is_active) VALUES (?, ?, ?, ?, 1)");
        $stmt->execute([$cat['name'], $cat['slug'], $cat['description'], $cat['order']]);
        $categoryIds[$cat['slug']] = $pdo->lastInsertId();
        echo "Created category: {$cat['name']}\n";
    }
    
    // Step 4: Insert menu items
    echo "Step 4: Inserting menu items...\n";
    
    // Helper function to parse price
    function parsePrice($priceStr) {
        // Remove 'MK', 'Mk', commas, and spaces, then convert to float
        $cleaned = str_replace(['MK', 'Mk', ',', ' '], '', $priceStr);
        return floatval($cleaned);
    }
    
    $menuItems = [
        // BREAKFAST
        ['category' => 'breakfast', 'name' => 'English Breakfast', 'price' => 'MK35,000.00', 'description' => 'A glass of home-made juice or orange squash, boiled oats or rice porridge, seasonal fruits, two farm fresh eggs done on your perfection. Sunnyside up, full house omelette or scrambled, poached, egg white only, grilled beef sausages, grilled tomato and chef garden vegetables toasted bread with butter, zitumbuwa, pancakes, doughnuts or mandasi. Sweet potatoes or Cassava tea or coffee.', 'order' => 1],
        
        // STARTER
        ['category' => 'starter', 'name' => 'Mushroom Soup', 'price' => 'MK15,000.00', 'description' => 'Classic French style cream of mushroom soup served with garlic butter toasted panin', 'order' => 1],
        ['category' => 'starter', 'name' => 'Italian Style Tomato Soup', 'price' => 'MK15,000.00', 'description' => 'Slow roasted puree of tomatoes and a touch of cream and balsamic vinegar with garlic buttered toasted panin.', 'order' => 2],
        ['category' => 'starter', 'name' => 'Green Salad', 'price' => 'MK10,000.00', 'description' => 'Fresh and crispy lettuce with fresh onion, tomato & cucumber', 'order' => 3],
        ['category' => 'starter', 'name' => 'Sun Hotel Greek Salad', 'price' => 'MK12,000.00', 'description' => 'Fresh from the garden with calamata, olives, feta cheese, tomato wedges, sliced red onions rings, crispy cucumbers, oregano and merange of lettuce with an Italian herb-based dressing', 'order' => 4],
        ['category' => 'starter', 'name' => 'Tempura Prawns', 'price' => 'MK17,000.00', 'description' => 'Fried prawns in a crispy butter served on Asian -style vegetables with branched noodles and accompanied with mustard cream reduction.', 'order' => 5],
        ['category' => 'starter', 'name' => 'Chicken liver Masala', 'price' => 'MK17,000.00', 'description' => 'Stew cooked in authentic Indian spices served with garlic butter, naan bread with deep fried onion and fresh onion.', 'order' => 6],
        ['category' => 'starter', 'name' => 'Hot Snack Platter', 'price' => 'MK20,500.00', 'description' => 'Two beef samosa & two chicken wingless, two beef meatballs, served with sambay cele apricot chili chutney and cajun potato.', 'order' => 7],
        
        // CHICKEN CORNER
        ['category' => 'chicken-corner', 'name' => 'Chicken peri-peri', 'price' => 'MK22,000.00', 'description' => 'Juicy and Tender grilled 1/4-chicken marinated in a peri-peri sauce, served with a choice of green salads or mixed vegetables. Served with a choice of French fries/baked Potatoes/Mashed Potatoes/Rice and Seasonal Vegetables', 'order' => 1],
        ['category' => 'chicken-corner', 'name' => 'Boiled Chicken Curry', 'price' => 'MK22,000.00', 'description' => 'Tender and succulent pieces of chicken swimming in a super flavourful & delicious curry sauce. Served with a choice of French fries/baked Potatoes/Mashed Potatoes/Rice and Seasonal Vegetables', 'order' => 2],
        ['category' => 'chicken-corner', 'name' => 'Chicken Stir-fry', 'price' => 'MK22,000.00', 'description' => 'Incredibly tender, juicy, moist and outrageously delicious pan fried chicken breast. Served with a choice of French fries/baked Potatoes/Mashed Potatoes/Rice and Seasonal Vegetables', 'order' => 3],
        ['category' => 'chicken-corner', 'name' => 'Grilled ¼ Chicken', 'price' => 'MK22,000.00', 'description' => 'Quarter of a delicious tender grilled chicken pairs perfectly with your choice of flavour and sides. Served with a choice of French fries/baked Potatoes/Mashed Potatoes/Rice and Seasonal Vegetables', 'order' => 4],
        ['category' => 'chicken-corner', 'name' => 'Chicken Khwasu', 'price' => 'MK22,000.00', 'description' => 'Well grilled chicken pieces and pan fried with garlic, green pepper, onions finshed with mango archer sauce. Served with a choice of French fries/baked Potatoes/Mashed Potatoes/Rice and Seasonal Vegetables', 'order' => 5],
        ['category' => 'chicken-corner', 'name' => 'Local Chicken', 'price' => 'MK22,000.00', 'description' => 'Grandmothers favourite stewed road runner chicken. Served with a choice of French fries/baked Potatoes/Mashed Potatoes/Rice and Seasonal Vegetables', 'order' => 6],
        
        // MEAT CORNER
        ['category' => 'meat-corner', 'name' => 'T Bone Steak', 'price' => 'MK28,000.00', 'description' => 'Well marinated and seasoned T Bone steak grilled to your choice', 'order' => 1],
        ['category' => 'meat-corner', 'name' => 'Beef Strips', 'price' => 'MK20,000.00', 'description' => 'Beef strips marinated in various spices and finshed in stroganoff sauce', 'order' => 2],
        ['category' => 'meat-corner', 'name' => 'Sirloin Steak', 'price' => 'MK24,000.00', 'description' => 'Tender sirloin steak grilled to your request served with homemade French fries', 'order' => 3],
        ['category' => 'meat-corner', 'name' => 'Goat Stew', 'price' => 'MK18,000.00', 'description' => 'Goat meat cutlets marinated and finshed in its own juice, finished with thick gravy sauce.', 'order' => 4],
        ['category' => 'meat-corner', 'name' => 'Beef Stew', 'price' => 'MK19,000.00', 'description' => 'Stewed beef well cooked in garlic and soy sauce finished with kick of mango acher.', 'order' => 5],
        ['category' => 'meat-corner', 'name' => 'Fillet Mignon', 'price' => 'MK35,000.00', 'description' => 'Grilled fillet mignon, served with fresh green salads and French fries, rice or nsima', 'order' => 6],
        
        // FISH CORNER
        ['category' => 'fish-corner', 'name' => 'Fish & Chips', 'price' => 'MK30,000.00', 'description' => 'Tradition fish fillets fried in a butter or grilled served with tartar sauce and chips. Served with a choice of French fries/Baked potatoes/Mashed Potatoes/Rice and Seasonal Vegetables', 'order' => 1],
        ['category' => 'fish-corner', 'name' => 'Grilled Chambo (open & whole)', 'price' => 'MK28,000.00', 'description' => 'Open or closed whole chambo fresh from lake Malawi spiced and marinated in lemon juice and fish spice. Served with a choice of French fries/Baked potatoes/Mashed Potatoes/Rice and Seasonal Vegetables', 'order' => 2],
        ['category' => 'fish-corner', 'name' => 'Grilled Kampango', 'price' => 'MK28,000.00', 'description' => 'Open marinated in fresh garlic, lemon juice and fish spices. Served with a choice of French fries/Baked potatoes/Mashed Potatoes/Rice and Seasonal Vegetables', 'order' => 3],
        ['category' => 'fish-corner', 'name' => 'Mama\'s Choice', 'price' => 'MK28,000.00', 'description' => 'Stewed chambo with green pepper, tomatoes and Onions. Served with a choice of French fries/Baked potatoes/Mashed Potatoes/Rice and Seasonal Vegetables', 'order' => 4],
        ['category' => 'fish-corner', 'name' => 'Sun prawn platter', 'price' => 'MK48,000.00', 'description' => 'Succulent Mozambican prawns, marinated in peri-peri basting or fried in atempura butter, served with garlic butter sauce, tartar sauce and pink sauce. Served with a choice of French fries/Baked potatoes/Mashed Potatoes/Rice and Seasonal Vegetables', 'order' => 5],
        
        // PASTA CORNER
        ['category' => 'pasta-corner', 'name' => 'Spaghetti Bolognese', 'price' => 'MK25,000.00', 'description' => 'Classic beef bolognese source served on a bed of dente cooked spaghetti and garnished with grated parmesan cheese', 'order' => 1],
        ['category' => 'pasta-corner', 'name' => 'Spaghetti Napolitano', 'price' => 'MK18,000.00', 'description' => 'Soft-cooked spaghetti, tomato ketchup, onion, button mushrooms, green peppers', 'order' => 2],
        ['category' => 'pasta-corner', 'name' => 'Chicken Alfredo', 'price' => 'MK20,000.00', 'description' => 'Cooked spaghetti fettucine pasta tosses with cream, garlic cheese sauce and oregano', 'order' => 3],
        ['category' => 'pasta-corner', 'name' => 'Asian Vegetables Stir fly', 'price' => 'MK22,000.00', 'description' => 'A melange of Asian vegetables cooked in light soy, garlic butter cumin and a hint of chilli with Chinese eggs noodles and dumbed peppers.', 'order' => 4],
        
        // BURGER CORNER
        ['category' => 'burger-corner', 'name' => 'Sun Hotel Burger', 'price' => 'MK20,000.00', 'description' => 'Fresh, avorful, at patty burger made from the nest Malawian beef served chips', 'order' => 1],
        ['category' => 'burger-corner', 'name' => 'Mega Double Burger', 'price' => 'MK30,000.00', 'description' => 'Juicy, big, loaded with toppings of your choice', 'order' => 2],
        ['category' => 'burger-corner', 'name' => 'Chicken Spice Burger', 'price' => 'MK25,000.00', 'description' => 'Crispy fried spicy chicken breast layered between, Brioche Bun, lettuce, cheese, gherkins and lashing of homemade spicy mayo sauce', 'order' => 3],
        
        // PIZZA CORNER
        ['category' => 'pizza-corner', 'name' => 'Barbeque Pizza Large', 'price' => 'MK36,000.00', 'description' => 'Classic with its sweet, tangy, and salty BBQ sauce, bits of juicy chicken, creamy cheese, and savoury onions', 'order' => 1],
        ['category' => 'pizza-corner', 'name' => 'Barbeque Pizza Medium', 'price' => 'MK32,000.00', 'description' => 'Classic with its sweet, tangy, and salty BBQ sauce, bits of juicy chicken, creamy cheese, and savoury onions', 'order' => 2],
        ['category' => 'pizza-corner', 'name' => 'Barbeque Pizza Small', 'price' => 'MK28,000.00', 'description' => 'Classic with its sweet, tangy, and salty BBQ sauce, bits of juicy chicken, creamy cheese, and savoury onions', 'order' => 3],
        ['category' => 'pizza-corner', 'name' => 'Vegetable Pizza Large', 'price' => 'MK30,000.00', 'description' => 'Fresh cherry tomatoes, bell peppers, artichoke, spinach and more', 'order' => 4],
        ['category' => 'pizza-corner', 'name' => 'Vegetable Pizza Medium', 'price' => 'MK25,000.00', 'description' => 'Fresh cherry tomatoes, bell peppers, artichoke, spinach and more', 'order' => 5],
        ['category' => 'pizza-corner', 'name' => 'Vegetable Pizza Small', 'price' => 'MK22,000.00', 'description' => 'Fresh cherry tomatoes, bell peppers, artichoke, spinach and more', 'order' => 6],
        ['category' => 'pizza-corner', 'name' => 'Chicken & Boerewors Pizza Large', 'price' => 'MK35,000.00', 'description' => 'Grilled boerewors, caramelized onion, mozzarella and fresh basil', 'order' => 7],
        ['category' => 'pizza-corner', 'name' => 'Chicken & Boerewors Medium Pizza', 'price' => 'MK30,000.00', 'description' => 'Grilled boerewors, caramelized onion, mozzarella and fresh basil', 'order' => 8],
        ['category' => 'pizza-corner', 'name' => 'Chicken & Boerewors Small', 'price' => 'MK28,000.00', 'description' => 'Grilled boerewors, caramelized onion, mozzarella and fresh basil', 'order' => 9],
        ['category' => 'pizza-corner', 'name' => 'Extra-Large Pizza (All varieties)', 'price' => 'MK42,000.00', 'description' => 'All extra-large pizza\'s', 'order' => 10],
        
        // SNACK CORNER
        ['category' => 'snack-corner', 'name' => 'Cajun Chicken Wings or Drum sticks', 'price' => 'MK15,500.00', 'description' => 'Southern fried chicken wings and drumstick with cajun seasoning freshly squeezed lemon, crispy chips and chefs style salad with dressing.', 'order' => 1],
        ['category' => 'snack-corner', 'name' => 'Meat Balls', 'price' => 'MK20,000.00', 'description' => 'Braised homemade meatballs served in barbeque sauce', 'order' => 2],
        ['category' => 'snack-corner', 'name' => 'Beef Samosa or Chicken Samosa', 'price' => 'MK18,000.00', 'description' => 'A very nice & juicy snack to go with your favourite beverage', 'order' => 3],
        ['category' => 'snack-corner', 'name' => 'Chicken wrap or Beef Wraps', 'price' => 'MK19,000.00', 'description' => 'Every bite is loaded with juicy flavourful chicken or beef cirantro- lime sautéed peppers and onions cool yoghurt served with chips.', 'order' => 4],
        ['category' => 'snack-corner', 'name' => 'Chicken Fingers', 'price' => 'MK15,500.00', 'description' => 'Crispy chicken tenders\' hand -breaded with a hint of spices served with a choice dipping sauce.', 'order' => 5],
        ['category' => 'snack-corner', 'name' => 'Deli-style Sandwich', 'price' => 'MK18,000.00', 'description' => 'Double cheese and tomatoes one chicken mayo/fried eggs gherkin with tartar sauce accompanied with crispy sliced potatoes, a choice of toasted of brown or white bread.', 'order' => 6],
        ['category' => 'snack-corner', 'name' => 'Sausages', 'price' => 'MK16,000.00', 'description' => 'Gilled sausages served with fresh green salads and French fries.', 'order' => 7],
        ['category' => 'snack-corner', 'name' => 'Omelette or fried Eggs', 'price' => 'MK12,000.00', 'description' => 'Spanish omelette or fried eggs served with French fries and salads', 'order' => 8],
        ['category' => 'snack-corner', 'name' => 'Plain chips', 'price' => 'MK10,000.00', 'description' => 'Freshly made French fries served with green salads', 'order' => 9],
        ['category' => 'snack-corner', 'name' => 'Chicken Chiwamba (whole)', 'price' => 'MK40,000.00', 'description' => 'Charcoal grilled local chicken marinated in garlic & lemon juice and chicken spice served with green salads & chips', 'order' => 10],
        
        // INDIAN CORNER
        ['category' => 'indian-corner', 'name' => 'Fish Curry', 'price' => 'Mk28,000.00', 'description' => 'Fresh stewed chambo in a curry sauce and other vegetables served with a choice of rice, nsima or chips', 'order' => 1],
        ['category' => 'indian-corner', 'name' => 'Chicken Butter', 'price' => 'Mk23,500.00', 'description' => 'Pieces of chicken, tossed in a simple spice marinade, light, buttery, creamy tomato sauce served with a choice of chips, nsima or rice', 'order' => 2],
        ['category' => 'indian-corner', 'name' => 'Beef Curry', 'price' => 'Mk24,500.00', 'description' => 'With succulent meat cooked in aromatic spices, this Indian style beef curry, served with rice, nsima or chips', 'order' => 3],
        ['category' => 'indian-corner', 'name' => 'Goat Curry', 'price' => 'Mk24,500.00', 'description' => 'Tender and juicy goatmeat, finished in its own juice & curry sauce', 'order' => 4],
        ['category' => 'indian-corner', 'name' => 'Biriyani Rice', 'price' => 'Mk27,000.00', 'description' => 'A flavourful fragrance of Kilombero rice with tender marinated meat and a blend of spices cooked in a clay pot known as matka', 'order' => 5],
        
        // LIWONDE SUN SPECIALITIES
        ['category' => 'liwonde-sun-specialities', 'name' => 'Jollof Rice', 'price' => 'Mk34,000.00', 'description' => 'A flavourful fragrance of Kilombero rice with tender marinated meat and a blend of spices cooked in a clay pot known as matka', 'order' => 1],
        ['category' => 'liwonde-sun-specialities', 'name' => 'Okra Soup', 'price' => 'Mk34,000.00', 'description' => 'A flavourful fragrance of Kilombero rice with tender marinated meat and a blend of spices cooked in a clay pot known as matka', 'order' => 2],
        
        // EXTRAS
        ['category' => 'extras', 'name' => 'Plain Chapati', 'price' => 'Mk7,000.00', 'description' => 'Plain chapati', 'order' => 1],
        ['category' => 'extras', 'name' => 'Plain Nsima', 'price' => 'Mk7,000.00', 'description' => 'Plain nsima', 'order' => 2],
        ['category' => 'extras', 'name' => 'Plain Rice', 'price' => 'Mk7,000.00', 'description' => 'Plain rice', 'order' => 3],
        ['category' => 'extras', 'name' => 'Plain Chips', 'price' => 'Mk10,000.00', 'description' => 'Plain chips', 'order' => 4],
        ['category' => 'extras', 'name' => 'Beef or Chicken Samosa Only (4)', 'price' => 'Mk10,000.00', 'description' => 'Beef or chicken samosa only (4 pieces)', 'order' => 5],
        ['category' => 'extras', 'name' => 'Extra Vegetable/Beans', 'price' => 'Mk7,000.00', 'description' => 'Extra vegetable or beans', 'order' => 6],
        
        // DESSERTS
        ['category' => 'desserts', 'name' => 'Banana Custard', 'price' => 'MK15,000.00', 'description' => 'Amazing dessert to binge on when craving for something sweet.', 'order' => 1],
        ['category' => 'desserts', 'name' => 'Milk Shake', 'price' => 'MK8,500.00', 'description' => 'Natural vanilla bean ice cream, dark chocolate truffle sauce, freshly whipped cream and topped with an all-natural Bing cherry', 'order' => 2],
        ['category' => 'desserts', 'name' => 'Fruit of the Day', 'price' => 'MK5,000.00', 'description' => 'Enjoy seasonal fruit of your choice, banana, apples, oranges', 'order' => 3],
        ['category' => 'desserts', 'name' => 'Ice Cream Cup', 'price' => 'MK5,000.00', 'description' => 'Enjoy a choice, different ice cream flavours, strawberry, vanilla and chocolate.', 'order' => 4],
        ['category' => 'desserts', 'name' => 'Chocolate Gateaux', 'price' => 'MK15,000.00', 'description' => 'Chocolate gateaux', 'order' => 5],
        ['category' => 'desserts', 'name' => 'Fruit Salads (bowl)', 'price' => 'MK12,000.00', 'description' => 'Fresh fruit salad bowl', 'order' => 6]
    ];
    
    $stmt = $pdo->prepare("INSERT INTO food_menu (category, item_name, description, price, currency_code, is_available, is_featured, display_order) VALUES (?, ?, ?, ?, 'MWK', 1, 0, ?)");
    
    foreach ($menuItems as $item) {
        $categoryId = $categoryIds[$item['category']];
        $price = parsePrice($item['price']);
        
        $stmt->execute([
            $categoryId,
            $item['name'],
            $item['description'],
            $price,
            $item['order']
        ]);
        
        echo "Added: {$item['name']} - MK" . number_format($price, 2) . "\n";
    }
    
    // Step 5: Update site settings with phone numbers
    echo "\nStep 5: Updating site settings with phone numbers...\n";
    
    $settings = [
        'phone_main' => '0212 877 796',
        'phone_reception' => '0883 500 304',
        'phone_cell1' => '0998 864 377',
        'phone_cell2' => '0882 363 765',
        'phone_alternate1' => '0983 825 196',
        'phone_alternate2' => '0999 877 796',
        'phone_alternate3' => '0888 353 540',
        'email_restaurant' => 'liwondesunhotel@gmail.com'
    ];
    
    $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value, setting_group, updated_at) VALUES (?, ?, 'contact', NOW()) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = NOW()");
    
    foreach ($settings as $key => $value) {
        $stmt->execute([$key, $value]);
        echo "Updated setting: $key = $value\n";
    }
    
    // Commit transaction
    $pdo->commit();
    
    echo "\n========================================\n";
    echo "Menu update completed successfully!\n";
    echo "========================================\n";
    echo "- Deleted $deletedItems old menu items\n";
    echo "- Deleted $deletedCats old categories\n";
    echo "- Created " . count($categories) . " new categories\n";
    echo "- Added " . count($menuItems) . " menu items\n";
    echo "- Updated " . count($settings) . " site settings\n";
    echo "\nPhone numbers added:\n";
    foreach ($settings as $key => $value) {
        if (strpos($key, 'phone') !== false) {
            echo "  $key: $value\n";
        }
    }
    echo "\nEmail added: liwondesunhotel@gmail.com\n";
    
} catch (PDOException $e) {
    if ($pdo !== null && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>