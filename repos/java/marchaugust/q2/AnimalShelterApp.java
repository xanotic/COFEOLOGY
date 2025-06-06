package q2;
public class AnimalShelterApp {
    public static void main(String[] args){
        AnimalShelter AnimalShelter1 = new AnimalShelter("Lucky", 2, "Labrador Retriever"); 
        AnimalShelter1.setAvailability(true);
        AnimalShelter AnimalShelter2 = new AnimalShelter("Milo", 1, "German Shepherd");
        AnimalShelter2.setAvailability(false);

        int numAvailableAnimals = 0;
        if (AnimalShelter1.getAvailable())
            numAvailableAnimals++;
        if (AnimalShelter2.getAvailable())
            numAvailableAnimals++;
        System.out.println("number animal :"+ numAvailableAnimals);


    }
}
