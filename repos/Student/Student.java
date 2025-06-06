public class Student
{
  private String name;
  private char gender;
  private int age;
  private double fee;

  // default constructor
  public Student()
  {
    name = null;
    gender = 'M';
    age = 20;
  }

  // normal constructor
  public Student(String n, char g, int a, double b)
  {
    name = n;
    gender = g;
    age = a;
    fee = b;
  }

  //display
  public void displayStudent()
  {
    System.out.println("Name: " + name);
    System.out.println("Gender: " + gender);
    System.out.println("Age: " + age);
    System.out.println();

  }

  // mutator methods
  public void setName(String n)
  {
    name = n;
  }

  public void setGender(char g)
  {
    gender = g;
  }

  public void setAge(int a)
  {
    age = a;
  }
  public void setAge(double b)
  {
    fee = b;
  }

  //accessor methods
  public String getName()
  {
    return name;
  }

  public char getGender()
  {
    return gender;
  }

  public int getAge()
  {
    return age;
  }
  public double getFee()
  {
    return fee;
  }
  public double calcFee()
  {
    double y;
    if (age > 60)
        y = fee * 0.95;
    else
        y = fee;
    return y;
  }

  public void displayFee()
  {
    System.out.println("name :"+ name);
    System.out.println("gender :"+ gender);
    System.out.println("age :"+ age);
    System.out.println("fee :"+ fee);
  }

  public String toString()
  {
    return "Name :" + name + "Gender : " + gender + "age :" + age + "fee :" + fee;
  }



}