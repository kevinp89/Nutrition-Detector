

import json
def writeToFile(foodName, Calorie):
    """
    """
    with open("FoodToCalorie.json") as f:
        data = json.load(f)

    data.update({foodName: Calorie})

    with open("FoodToCalorie.json", "w") as file:
        json.dump(data, file)
    file.close()


food = input("input food: ")
cal = input("input calorie: ")

writeToFile(food, cal)


