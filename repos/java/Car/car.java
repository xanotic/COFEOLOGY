public class car { // Class names should start with an uppercase letter by convention
    private String brand;
    private String model;
    private int year;

    public void define() {
        brand = "Honda"; // Removed extra spaces
        model = "Accord"; // Removed extra spaces
        year = 2005;
    }

    public void display() {
        System.out.println("Brand name: " + brand +" | " +" Model name: " + model + " | " +" Year made: " + year); // Added spaces for better readability
    }

    public void selfDefine(String x, String y, int z) { // Renamed method to follow camelCase convention
        brand = x;
        model = y;
        year = z;
    }
}