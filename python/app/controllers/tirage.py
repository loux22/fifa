from flask import Blueprint, render_template, redirect, url_for, request
import mysql.connector
import random


fifa = Blueprint('fifa', __name__, url_prefix='/')

@fifa.route('/')
def index():
    return redirect(url_for('fifa.tirage'))



@fifa.route('/tirage', methods=['GET', 'POST'])
def tirage():
    chapeau = []
    continent = request.args.get('continent')

    teams = get_team_filtre()

    if not continent:
        continent = "tous"
    if not continent == "tous" :
        teams = [dico for dico in teams if dico.get('continent') == continent]

    if request.method == 'POST':
        n_team = int(request.form.get('n_team'))
        team_select = request.form.getlist('team_select')
        for i in range (0, int(n_team/4)):
            chapeau.append(team_select[4*i:4*(i+1)]) 

    poules = [chap.copy() for chap in chapeau]
    for chap in poules:
        random.shuffle(chap)

    return render_template('pages/tirage.html', teams=teams, continent=continent, chapeau=chapeau, poules=poules)





def connexion_bdd():
    cnx = mysql.connector.connect(user='root', password='',
                                host='127.0.0.1',
                                database='fifa')
    return cnx

def get_team_filtre():
    teams = []
    cur = connexion_bdd()
    if cur and cur.is_connected():
        with cur.cursor(dictionary=True) as cursor:
            result = cursor.execute(f"SELECT * FROM equipe ORDER BY points desc")
            rows = cursor.fetchall()
            for index,row in enumerate(rows):
                row["classement"] = index + 1
                teams.append(row)
        cur.close()
        
    else:
        print("Could not connect")
    return teams
