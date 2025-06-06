package q2;
public class AnimalShelter {
    private String name;
    private int age;
    private String breed;
    private boolean available;

    public AnimalShelter(String n, int a, String b){
        name = n;
        age = a;
        breed = b;
    }

    public String getName(){
        return name;
    }
    public int getAge(){
        return age;
    }
    public String getBreed(){
        return breed;
    }
    public boolean getAvailable(){
        return available;
    }

    public void setName(String n){
        name = n;
    }

    public void setAge(int a){
        age = a;
    }

    public void setBreed(String b){
        breed = b;
    }

    public void setAvailability(boolean available){
        this.available = available;
    }

    public boolean isAvailable(){
        if (getAvailable())
            return true;
        else
            return false;
    }

    public String toString() {
        String info = "\nName: "+ name + "\nAge: " + age +
                    "\nBreed: " + breed +
                    "\nAvailable for adoption: " + available;
        return info;
    }
}
