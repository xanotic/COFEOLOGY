package q3;

public class Lecturer {

    Lecturer[] arrLtr = new Lecturer [3]; 

    arrLtr[0] = new Lecturer("Suffian Omar", 175234, "Professor");
    arrLtr[1] = new Lecturer("Abdul Hadi Razman", 195745, "Senior Lecturer");
    arrLtr[2] = new Lecturer("Ayu Nazirah Ismail", 185678, "Associate Professor");


    Course hi = new Course("CSC253", "Multimedia", arrLtr);

}
